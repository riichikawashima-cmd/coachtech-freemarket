<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    public function edit($item_id)
    {
        $profile = null;

        return view('purchase.address.edit', compact('item_id', 'profile'));
    }

    public function update(AddressRequest $request, $item_id)
    {
        $data = $request->validated();

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
