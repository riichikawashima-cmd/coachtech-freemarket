<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;

class StripeCheckoutController extends Controller
{
    public function create($item_id)
    {
        $item = \App\Models\Item::findOrFail($item_id);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => (int) $item->price,
                    'product_data' => [
                        'name' => $item->name,
                    ],
                ],
            ]],
            'metadata' => [
                'item_id' => (string) $item->id,
            ],
            'success_url' => url('/checkout/success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => route('purchase.create', ['item_id' => $item->id]),
        ]);

        Log::info('stripe checkout created', [
            'session_id' => $session->id,
            'url' => $session->url,
        ]);

        return redirect($session->url);
    }

    public function success(\Illuminate\Http\Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect('/')->with('error', '決済情報が取得できませんでした。');
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        $itemId = $session->metadata->item_id ?? null;

        if (!$itemId) {
            return redirect('/')->with('error', '商品情報が取得できませんでした。');
        }

        $already = \App\Models\Purchase::where('item_id', $itemId)->exists();
        if ($already) {
            return redirect('/')->with('error', 'この商品は既に購入されています。');
        }

        $profile = auth()->user()->profile;

        $shipping = session('purchase_address') ?? [
            'postal_code'   => $profile->postal_code ?? '',
            'address'       => $profile->address ?? '',
            'building_name' => $profile->building_name ?? null,
        ];

        \App\Models\Purchase::create([
            'user_id' => auth()->id(),
            'item_id' => $itemId,
            'payment_method' => 'card',
            'postal_code' => $shipping['postal_code'],
            'address' => $shipping['address'],
            'building_name' => $shipping['building_name'],
        ]);

        session()->forget('purchase_address');

        return redirect('/')->with('success', '購入が完了しました。');
    }
}
