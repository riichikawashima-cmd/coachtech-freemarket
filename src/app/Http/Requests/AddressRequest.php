<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'postal_code'   => ['required'],
            'address'       => ['required'],
            'building_name' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'postal_code.required' => '郵便番号を入力してください',
            'address.required'     => '住所を入力してください',
        ];
    }

    public function attributes(): array
    {
        return [
            'postal_code'   => '郵便番号',
            'address'       => '住所',
            'building_name' => '建物名',
        ];
    }
}
