<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');       // recommend | mylist
        $keyword = $request->query('keyword', null);      // 検索キーワード（部分一致）

        // おすすめ（FN014）
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

        // マイリスト（FN015）：未認証は何も表示しない
        if (!Auth::check()) {
            $items = collect();
            return view('index', compact('items'));
        }

        // マイリスト：いいねした商品のみ表示（購入済みはSold判定）
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
        // 商品（いいね数・コメント数付き）
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

        // カテゴリ（複数）
        $categories = DB::table('categories')
            ->join('category_item', 'categories.id', '=', 'category_item.category_id')
            ->where('category_item.item_id', $item_id)
            ->select('categories.name')
            ->pluck('name');

        // コメント一覧（ユーザー名付き）
        $comments = DB::table('comments')
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->where('comments.item_id', $item_id)
            ->orderByDesc('comments.created_at')
            ->select([
                'comments.comment',
                'comments.created_at',
                'users.name as user_name',
            ])
            ->get();

        // ★ いいね済みかどうか
        $isLiked = Auth::check()
            ? DB::table('likes')
            ->where('user_id', Auth::id())
            ->where('item_id', $item_id)
            ->exists()
            : false;

        return view('items.show', compact('item', 'categories', 'comments', 'isLiked'));
    }
}
