<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
{
    public function create()
    {
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


    public function store(ExhibitionRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validated();

        $storedPath = $request->file('image')->store('items', 'public');
        $publicPath = 'storage/' . $storedPath;

        DB::transaction(function () use ($user, $data, $publicPath) {
            $itemId = DB::table('items')->insertGetId([
                'user_id' => $user->id,
                'name' => $data['name'],
                'brand' => $data['brand'] ?? '',
                'description' => $data['description'],
                'price' => $data['price'],
                'condition' => $data['condition'],
                'image_path' => $publicPath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($data['category_ids'] as $categoryId) {
                DB::table('category_item')->insert([
                    'item_id' => $itemId,
                    'category_id' => $categoryId,
                ]);
            }
        });

        return redirect('/');
    }
}
