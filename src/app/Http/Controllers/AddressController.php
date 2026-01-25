<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function edit($item_id)
    {
        // 常に空欄表示にしたいので、profileは使わない
        $profile = null;

        return view('purchase.address.edit', compact('item_id', 'profile'));
    }

    public function update(AddressRequest $request, $item_id)
    {
        $data = $request->validated();

        // プロフィールは更新しない
        // 購入時専用の配送先として session に保存
        session([
            'purchase_address' => [
                'postal_code'   => $data['postal_code'],
                'address'       => $data['address'],
                'building_name' => $data['building_name'] ?? null,
            ],
        ]);

        return redirect()->route('purchase.create', $item_id);
    }
}
