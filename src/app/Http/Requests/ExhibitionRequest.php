<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'mimes:jpeg,jpg,png'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer'],
            'condition' => ['required'],
            'price' => ['required', 'integer', 'min:0'],
            'brand' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_ids.required' => 'カテゴリーを選択してください。',
            'category_ids.array' => 'カテゴリーの形式が不正です。',
            'category_ids.min' => 'カテゴリーを1つ以上選択してください。',
        ];
    }
}
