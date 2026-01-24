<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class SellController extends Controller
{
    public function create()
    {
        // 「戻る(withInput)」じゃない＝新規で出品画面に来たときは、
        // 前回の一時画像（session + tmpファイル）を消す
        if (!session()->hasOldInput()) {
            $publicPath = session('sell_confirm.image_path'); // storage/items_tmp/xxx.jpg

            if ($publicPath) {
                $tmp = str_replace('storage/', '', $publicPath); // items_tmp/xxx.jpg
                Storage::disk('public')->delete($tmp);
            }

            session()->forget('sell_confirm.image_path');
        }

        $categories = DB::table('categories')
            ->select('id', 'name')
            ->orderBy('id')
            ->get();

        $conditions = DB::table('conditions')
            ->select('id', 'name')
            ->orderBy('id')
            ->get();

        return view('sell.create', compact('categories', 'conditions'));
    }


    /**
     * 出品内容確認
     */
    public function confirm(ExhibitionRequest $request)
    {
        $data = $request->validated();

        // ① まず session の一時画像があるか見る（戻る直後はこっち）
        $publicPath = session('sell_confirm.image_path');

        // ② 新しく画像が送られてきた時だけ store し直す
        if ($request->hasFile('image')) {
            $storedPath = $request->file('image')->store('items_tmp', 'public');
            $publicPath = 'storage/' . $storedPath;

            session(['sell_confirm.image_path' => $publicPath]);
        }

        // ③ どっちも無いなら（本当に画像無し）→ 出品画面へ戻す
        if (!$publicPath) {
            return redirect('/sell')->withErrors(['image' => '商品画像を選択してください'])->withInput();
        }

        return view('sell.confirm', compact('data', 'publicPath'));
    }

    /**
     * 出品確定
     */
    public function store(ExhibitionRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validated();

        // confirm経由ならsessionの画像を使う
        $publicPath = session('sell_confirm.image_path');

        if ($publicPath) {
            $tmp = str_replace('storage/', '', $publicPath);      // items_tmp/xxx.jpg
            $new = str_replace('items_tmp/', 'items/', $tmp);     // items/xxx.jpg

            Storage::disk('public')->move($tmp, $new);
            $publicPath = 'storage/' . $new;

            session()->forget('sell_confirm.image_path');
        } else {
            // confirmを通らなかった場合の保険
            $storedPath = $request->file('image')->store('items', 'public');
            $publicPath = 'storage/' . $storedPath;
        }

        DB::transaction(function () use ($user, $data, $publicPath) {
            $itemId = DB::table('items')->insertGetId([
                'user_id'    => $user->id,
                'name'       => $data['name'],
                'brand'      => $data['brand'] ?? '',
                'description' => $data['description'],
                'price'      => $data['price'],
                'condition'  => $data['condition'],
                'image_path' => $publicPath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($data['category_ids'] as $categoryId) {
                DB::table('category_item')->insert([
                    'item_id'     => $itemId,
                    'category_id' => $categoryId,
                ]);
            }
        });

        return redirect('/');
    }

    public function back(Request $request)
    {
        $data = $request->except('_token');

        return redirect('/sell')->withInput($data);
    }
}
