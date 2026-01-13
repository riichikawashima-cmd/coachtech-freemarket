<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 出品した商品
        $sellItems = Item::where('user_id', $user->id)->get();

        // 購入した商品
        $buyItemIds = Purchase::where('user_id', $user->id)->pluck('item_id');
        $buyItems = Item::whereIn('id', $buyItemIds)->get();

        return view('mypage.index', compact('user', 'sellItems', 'buyItems'));
    }
}
