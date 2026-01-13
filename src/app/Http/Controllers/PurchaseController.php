<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function create($item_id)
    {
        $item = Item::findOrFail($item_id);
        $profile = Auth::user()->profile;

        // condition(数字) → conditions.name(日本語)
        $conditionName = DB::table('conditions')
            ->where('id', $item->condition)
            ->value('name');

        if ($conditionName) {
            $item->condition = $conditionName;
        }

        // すでに購入済みなら購入画面に入れない
        $alreadyPurchased = Purchase::where('item_id', $item_id)->exists();
        if ($alreadyPurchased) {
            return redirect()->route('item.show', $item_id)
                ->withErrors(['purchase' => 'この商品はすでに購入されています']);
        }

        return view('purchase.create', compact('item', 'profile'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($item_id, $data) {
                $exists = Purchase::where('item_id', $item_id)
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    throw new \RuntimeException('ALREADY_PURCHASED');
                }

                Purchase::create([
                    'user_id' => Auth::id(),
                    'item_id' => $item_id,
                    'payment_method' => $data['payment_method'],
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'ALREADY_PURCHASED') {
                return back()->withErrors(['purchase' => 'この商品はすでに購入されています']);
            }
            throw $e;
        }

        return redirect('/');
    }
}
