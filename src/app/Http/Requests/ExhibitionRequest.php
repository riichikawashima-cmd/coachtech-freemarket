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
            'image.required' => '商品画像を選択してください',
            'image.mimes' => '商品画像はjpegまたはpng形式でアップロードしてください',

            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',

            'category_ids.required' => 'カテゴリーを選択してください',
            'category_ids.array' => 'カテゴリーの形式が不正です',
            'category_ids.min' => 'カテゴリーを1つ以上選択してください',

            'condition.required' => '商品の状態を選択してください',

            'price.required' => '価格を入力してください',
            'price.integer' => '価格は数値で入力してください',
            'price.min' => '価格は0円以上で入力してください',
        ];
    }
}
