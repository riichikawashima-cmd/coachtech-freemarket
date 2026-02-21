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
        if (!session()->hasOldInput()) {
            $publicPath = session('sell_confirm.image_path');

            if ($publicPath) {
                $tmp = str_replace('storage/', '', $publicPath);
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

    public function confirm(ExhibitionRequest $request)
    {
        $data = $request->validated();

        $publicPath = session('sell_confirm.image_path');

        if ($request->hasFile('image')) {
            $storedPath = $request->file('image')->store('items_tmp', 'public');
            $publicPath = 'storage/' . $storedPath;

            session(['sell_confirm.image_path' => $publicPath]);
        }

        if (!$publicPath) {
            return redirect('/sell')->withErrors(['image' => '商品画像を選択してください'])->withInput();
        }

        return view('sell.confirm', compact('data', 'publicPath'));
    }

    public function store(ExhibitionRequest $request)
    {
        $user = Auth::user();

        $data = $request->validated();

        $publicPath = session('sell_confirm.image_path');

        if ($publicPath) {
            $tmp = str_replace('storage/', '', $publicPath);
            $new = str_replace('items_tmp/', 'items/', $tmp);

            Storage::disk('public')->move($tmp, $new);
            $publicPath = 'storage/' . $new;

            session()->forget('sell_confirm.image_path');
        } else {
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
