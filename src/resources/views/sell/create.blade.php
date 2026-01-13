@extends('layouts.app')

@section('title', '商品の出品')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endpush

@section('content')
<div class="sell-container">
    <h1 class="sell-title">商品の出品</h1>

    {{-- まとめて表示は消す（各項目の下に出す） --}}

    <form method="POST" action="/sell" enctype="multipart/form-data">
        @csrf

        {{-- 商品画像 --}}
        <div class="sell-section">
            <label class="sell-label">商品画像</label>
            <div class="sell-image-box">
                <label class="sell-image-button">
                    画像を選択する
                    <input type="file" name="image" accept="image/png,image/jpeg" hidden>
                </label>
            </div>
            @error('image')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 商品の詳細 --}}
        <div class="sell-section">
            <h2 class="sell-subtitle">商品の詳細</h2>

            <label class="sell-label">カテゴリー</label>
            <div class="sell-categories">
                @foreach ($categories as $category)
                <label class="category-check">
                    <input
                        type="checkbox"
                        name="category_ids[]"
                        value="{{ $category->id }}"
                        {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                    <span class="category">{{ $category->name }}</span>
                </label>
                @endforeach
            </div>
            @error('category_ids')
            <p class="error-text">{{ $message }}</p>
            @enderror

            <label class="sell-label">商品の状態</label>
            <div class="cselect" data-name="condition">
                {{-- 送信用 --}}
                <input type="hidden" name="condition" value="{{ old('condition', '') }}">

                {{-- 表示エリア（閉じてる時） --}}
                <button type="button" class="cselect__button" aria-haspopup="listbox" aria-expanded="false">
                    <span class="cselect__text">
                        {{ old('condition') ? ($conditions->firstWhere('id', (int)old('condition'))->name ?? '選択してください') : '選択してください' }}
                    </span>
                    <span class="cselect__chev">▾</span>
                </button>

                {{-- 開いた時のリスト --}}
                <ul class="cselect__list" role="listbox">
                    @foreach ($conditions as $condition)
                    <li class="cselect__option"
                        role="option"
                        data-value="{{ $condition->id }}">
                        {{ $condition->name }}
                    </li>
                    @endforeach
                </ul>
            </div>

            @error('condition')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 商品名と説明 --}}
        <div class="sell-section">
            <h2 class="sell-subtitle">商品名と説明</h2>

            <label class="sell-label">商品名</label>
            <input class="sell-input" type="text" name="name" value="{{ old('name') }}">
            @error('name')
            <p class="error-text">{{ $message }}</p>
            @enderror

            <label class="sell-label">ブランド名</label>
            <input class="sell-input" type="text" name="brand" value="{{ old('brand') }}">
            @error('brand')
            <p class="error-text">{{ $message }}</p>
            @enderror

            <label class="sell-label">商品の説明</label>
            <textarea class="sell-textarea" name="description">{{ old('description') }}</textarea>
            @error('description')
            <p class="error-text">{{ $message }}</p>
            @enderror

            <label class="sell-label">販売価格</label>
            <input class="sell-input" type="number" name="price" placeholder="¥" value="{{ old('price') }}">
            @error('price')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="sell-submit">出品する</button>
    </form>
</div>
@endsection