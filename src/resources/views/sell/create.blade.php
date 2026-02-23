@extends('layouts.app')

@section('title', '商品の出品')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endpush

@section('content')
@php
$tmpImage = session('sell_confirm.image_path');
@endphp

<div class="sell-container">
    <h1 class="sell-title">商品の出品</h1>

    <form method="POST" action="{{ route('sell.confirm') }}" enctype="multipart/form-data">
        @csrf

        <div class="sell-section">
            <label class="sell-label">商品画像</label>

            <div class="sell-image-box">
                <img
                    id="sellImagePreview"
                    class="sell-image-preview"
                    alt="選択された画像プレビュー"
                    src="{{ $tmpImage ? asset($tmpImage) : '' }}"
                    @if($tmpImage) style="display:block;" @else style="display:none;" @endif>

                <label class="sell-image-button" id="sellImageButton">
                    <span class="sell-image-text" id="sellImageButtonText">
                        {{ $tmpImage ? '画像を変更する' : '画像を選択する' }}
                    </span>
                    <input
                        id="sellImageInput"
                        type="file"
                        name="image"
                        accept="image/png,image/jpeg"
                        hidden>
                </label>
            </div>

            @error('image')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

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
                <input type="hidden" name="condition" value="{{ old('condition', '') }}">

                <button type="button" class="cselect__button">
                    <span class="cselect__text">
                        {{ old('condition') ? ($conditions->firstWhere('id', (int)old('condition'))->name ?? '選択してください') : '選択してください' }}
                    </span>
                    <span class="cselect__chev">▾</span>
                </button>

                <ul class="cselect__list">
                    @foreach ($conditions as $condition)
                    <li class="cselect__option"
                        data-value="{{ $condition->id }}">
                        <span class="cselect__check">✓</span>
                        <span class="cselect__option-text">{{ $condition->name }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            @error('condition')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

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
            <div class="price-input">
                <span class="price-symbol">¥</span>
                <input
                    class="sell-input price-input__field"
                    type="text"
                    id="price"
                    name="price"
                    value="{{ old('price') }}">
            </div>

            @error('price')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="sell-submit">出品する</button>
    </form>
</div>

<script>
    const input = document.getElementById('sellImageInput');
    const preview = document.getElementById('sellImagePreview');
    const text = document.getElementById('sellImageButtonText');

    if (input && preview && text) {
        const saveState = () => ({
            src: preview.src,
            visible: preview.style.display === 'block',
        });

        const restoreState = (state) => {
            preview.src = state.src;
            preview.style.display = state.visible ? 'block' : 'none';
            text.textContent = state.visible ? '画像を変更する' : '画像を選択する';
        };

        let prev = saveState();
        let waitingDialogClose = false;

        // ダイアログを開く直前に状態保存
        input.addEventListener('click', () => {
            prev = saveState();
            waitingDialogClose = true;
        });

        // 通常の選択時（changeが出る環境）
        input.addEventListener('change', (e) => {
            waitingDialogClose = false;

            const file = e.target.files && e.target.files[0];
            if (!file) {
                restoreState(prev);
                return;
            }

            const reader = new FileReader();
            reader.onload = () => {
                preview.src = reader.result;
                preview.style.display = 'block';
                text.textContent = '画像を変更する';
            };
            reader.readAsDataURL(file);
        });

        // キャンセル時にchangeが出ない環境向け：ダイアログが閉じて戻ってきた瞬間に判定
        window.addEventListener('focus', () => {
            if (!waitingDialogClose) return;

            waitingDialogClose = false;

            // ファイルが選ばれてない（＝キャンセル）なら元に戻す
            if (!input.files || input.files.length === 0) {
                restoreState(prev);
            }
        });
    }
</script>

<script>
    (() => {
        const form = document.querySelector('form');
        const priceInput = document.getElementById('price');
        if (!form || !priceInput) return;

        const showError = (msg) => {
            let p = document.getElementById('priceErrorClient');
            if (!p) {
                p = document.createElement('p');
                p.id = 'priceErrorClient';
                p.className = 'error-text';
                const wrap = priceInput.closest('.price-input');
                wrap.insertAdjacentElement('afterend', p);
            }
            p.textContent = msg;
            p.style.display = 'block';
        };

        const clearError = () => {
            const p = document.getElementById('priceErrorClient');
            if (p) {
                p.textContent = '';
                p.style.display = 'none';
            }
        };

        form.addEventListener('submit', (e) => {
            clearError();
            const v = (priceInput.value || '').trim();
            if (v === '') return;

            if (v.includes('.') || !/^\d+$/.test(v)) {
                e.preventDefault();
                showError('価格は整数で入力してください。');
            }
        });

        priceInput.addEventListener('input', clearError);
    })();
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const wrap = document.querySelector('.cselect[data-name="condition"]');
        if (!wrap) return;

        const hidden = wrap.querySelector('input[type="hidden"][name="condition"]');
        const textEl = wrap.querySelector('.cselect__text');
        if (!hidden || !textEl) return;

        const v = (hidden.value || '').trim();
        if (!v) return;

        const opt = wrap.querySelector(`.cselect__option[data-value="${v}"] .cselect__option-text`);
        if (opt) textEl.textContent = opt.textContent.trim();
    });
</script>
@endsection