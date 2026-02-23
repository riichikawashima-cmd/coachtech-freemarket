<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword', null);

        if ($tab === 'recommend') {
            $items = DB::table('items')
                ->leftJoin('purchases', 'items.id', '=', 'purchases.item_id')
                ->select([
                    'items.*',
                    DB::raw('CASE WHEN purchases.id IS NULL THEN 0 ELSE 1 END AS is_sold'),
                ])
                ->when($keyword, function ($query) use ($keyword) {
                    return $query->where('items.name', 'like', '%' . $keyword . '%');
                })
                ->when(Auth::check(), function ($query) {
                    return $query->where('items.user_id', '!=', Auth::id());
                })
                ->orderByDesc('items.created_at')
                ->get();

            return view('index', compact('items'));
        }

        if (!Auth::check()) {
            $items = collect();
            return view('index', compact('items'));
        }

        $items = DB::table('items')
            ->join('likes', 'items.id', '=', 'likes.item_id')
            ->leftJoin('purchases', 'items.id', '=', 'purchases.item_id')
            ->where('likes.user_id', Auth::id())
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('items.name', 'like', '%' . $keyword . '%');
            })
            ->select([
                'items.*',
                DB::raw('CASE WHEN purchases.id IS NULL THEN 0 ELSE 1 END AS is_sold'),
            ])
            ->orderByDesc('likes.created_at')
            ->get();

        return view('index', compact('items'));
    }

    public function show($item_id)
    {
        $item = DB::table('items')
            ->where('items.id', $item_id)
            ->leftJoin('likes', 'items.id', '=', 'likes.item_id')
            ->leftJoin('comments', 'items.id', '=', 'comments.item_id')
            ->select([
                'items.*',
                DB::raw('COUNT(DISTINCT likes.id) AS likes_count'),
                DB::raw('COUNT(DISTINCT comments.id) AS comments_count'),
            ])
            ->groupBy('items.id')
            ->first();

        abort_if(!$item, 404);

        $conditionName = DB::table('conditions')
            ->where('id', $item->condition)
            ->value('name');

        if ($conditionName) {
            $item->condition = $conditionName;
        }

        $categories = DB::table('categories')
            ->join('category_item', 'categories.id', '=', 'category_item.category_id')
            ->where('category_item.item_id', $item_id)
            ->select('categories.name')
            ->pluck('name');

        $comments = DB::table('comments')
            ->leftJoin('users', 'comments.user_id', '=', 'users.id')
            ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('comments.item_id', $item_id)
            ->orderByDesc('comments.created_at')
            ->select([
                'comments.comment',
                'comments.created_at',
                DB::raw("COALESCE(users.name, 'ゲスト') AS user_name"),
                'profiles.image_path as image_path',
            ])
            ->paginate(5);

        $isLiked = Auth::check()
            ? DB::table('likes')
            ->where('user_id', Auth::id())
            ->where('item_id', $item_id)
            ->exists()
            : false;

        return view('items.show', compact('item', 'categories', 'comments', 'isLiked'));
    }
}
