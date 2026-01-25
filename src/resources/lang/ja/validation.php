<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Lines
    |--------------------------------------------------------------------------
    */

    'required' => ':attributeを入力してください',
    'email' => ':attributeはメール形式で入力してください',
    'integer' => ':attributeは整数で入力してください',
    'numeric' => ':attributeは数値で入力してください',
    'array' => ':attributeの形式が正しくありません',
    'mimes' => ':attributeは:values形式のファイルを指定してください',
    'unique' => 'この:attribute は既に使用されています。',

    'min' => [
        'numeric' => ':attributeは:min以上で入力してください',
        'array' => ':attributeは:min個以上選択してください',
    ],

    'max' => [
        'string' => ':attributeは:max文字以内で入力してください',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        // 認証系
        'email' => 'メールアドレス',
        'password' => 'パスワード',

        // 出品系
        'image' => '商品画像',
        'name' => '商品名',
        'description' => '商品説明',
        'category_ids' => 'カテゴリー',
        'condition' => '商品の状態',
        'price' => '販売価格',
        'brand' => 'ブランド名',
    ],
];
