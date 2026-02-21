<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'file', 'mimes:jpeg,jpg,png'],

            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer'],
            'condition' => ['required'],
            'price' => ['required', 'numeric', 'regex:/^\d+$/', 'min:0', 'max:9999999'],
            'brand' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $hasTmp = (bool) $this->session()->get('sell_confirm.image_path');
            $hasUpload = $this->hasFile('image');

            if (!$hasTmp && !$hasUpload) {
                $validator->errors()->add('image', '商品画像を選択してください');
            }
        });
    }

    public function messages(): array
    {
        return [
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
            'price.regex' => '価格は整数で入力してください',
            'price.min' => '価格は0円以上で入力してください',
            'price.max' => '価格は9,999,999円以下で入力してください',
        ];
    }

    protected function failedValidation(ValidatorContract $validator): void
    {
        if ($this->hasFile('image')) {
            $file = $this->file('image');

            if ($file && $file->isValid()) {
                $mime = $file->getMimeType();
                $ok = in_array($mime, ['image/jpeg', 'image/png'], true);

                if ($ok) {
                    $storedPath = $file->store('items_tmp', 'public');
                    $publicPath = 'storage/' . $storedPath;

                    $this->session()->put('sell_confirm.image_path', $publicPath);
                }
            }
        }

        throw new HttpResponseException(
            redirect('/sell')
                ->withErrors($validator)
                ->withInput()
        );
    }
}
