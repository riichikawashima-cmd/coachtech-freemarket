<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function create($item_id)
    {
        session()->forget('purchase_payment_method');
        session()->forget('purchase_payment_method_label');

        $item = Item::findOrFail($item_id);
        $profile = Auth::user()->profile;

        $shipping = session('purchase_address') ?? [
            'postal_code'   => $profile->postal_code ?? '',
            'address'       => $profile->address ?? '',
            'building_name' => $profile->building_name ?? '',
        ];

        $conditionName = DB::table('conditions')
            ->where('id', $item->condition)
            ->value('name');

        if ($conditionName) {
            $item->condition = $conditionName;
        }

        $alreadyPurchased = Purchase::where('item_id', $item_id)->exists();
        if ($alreadyPurchased) {
            return redirect()->route('item.show', $item_id)
                ->withErrors(['purchase' => 'この商品はすでに購入されています']);
        }

        return view('purchase.create', compact('item', 'profile', 'shipping'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $data = $request->validated();

        abort_if(Item::where('id', $item_id)->value('user_id') === Auth::id(), 403);

        if (($data['payment_method'] ?? null) === 'card') {
            return redirect()->route('purchase.stripe', $item_id);
        }

        $profile = Auth::user()->profile;

        $shipping = session('purchase_address') ?? [
            'postal_code'   => $profile->postal_code ?? '',
            'address'       => $profile->address ?? '',
            'building_name' => $profile->building_name ?? null,
        ];

        try {
            DB::transaction(function () use ($item_id, $data, $shipping) {
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
                    'postal_code' => $shipping['postal_code'],
                    'address' => $shipping['address'],
                    'building_name' => $shipping['building_name'],
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'ALREADY_PURCHASED') {
                return back()->withErrors(['purchase' => 'この商品はすでに購入されています']);
            }
            throw $e;
        }

        session()->forget('purchase_address');
        return redirect('/');
    }

    public function savePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:convenience,card',
            'label' => 'required|string',
        ]);

        session([
            'purchase_payment_method' => $request->payment_method,
            'purchase_payment_method_label' => $request->label,
        ]);

        return response()->json(['ok' => true]);
    }
}
