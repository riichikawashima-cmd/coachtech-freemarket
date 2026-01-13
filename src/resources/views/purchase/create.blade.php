@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('title', '商品購入')

@section('content')
<section class="purchase">
    <div class="purchase__container">
        {{-- 左カラム --}}
        <div class="purchase__main">
            <div class="purchase__item">
                <div class="purchase__item-image">
                    <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}">
                </div>

                <div class="purchase__item-info">
                    <p class="purchase__item-name">{{ $item->name }}</p>
                    <p class="purchase__item-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            {{-- 左側は「入力だけ」 --}}
            <div class="purchase__form">
                <div class="purchase__block">
                    <p class="purchase__block-title">支払い方法</p>

                    <select name="payment_method" class="purchase__select" form="purchaseForm" required>
                        <option value="">選択してください</option>
                        <option value="convenience">コンビニ支払い</option>
                        <option value="card">カード支払い</option>
                    </select>
                </div>

                <div class="purchase__block">
                    <div class="purchase__address-head">
                        <p class="purchase__block-title">配送先</p>
                        <a href="{{ route('purchase.address.edit', $item->id) }}" class="purchase__address-link">
                            変更する
                        </a>
                    </div>

                    <div class="purchase__address">
                        <p>〒 {{ $profile->postal_code ?? $profile->postcode ?? 'XXX-YYYY' }}</p>
                        <p>{{ $profile->address ?? 'ここには住所と建物が入ります' }}</p>
                        @if (!empty($profile->building_name))
                        <p>{{ $profile->building_name }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 右カラム --}}
        <aside class="purchase__summary">
            <div class="purchase__summary-box">
                <div class="purchase__summary-row">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </div>

                <div class="purchase__summary-row">
                    <span>支払い方法</span>
                    <span>コンビニ支払い</span>
                </div>
            </div>

            {{-- 右側に購入ボタン（フォーム送信） --}}
            <form id="purchaseForm" method="POST" action="{{ url('/purchase/' . $item->id) }}">
                @csrf
                <button type="submit" class="purchase__button">購入する</button>
            </form>
        </aside>
    </div>
</section>
@endsection