@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('title', '商品購入')

@section('content')
<section class="purchase">
    <div class="purchase__container">
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

            <div class="purchase__form">
                <div class="purchase__block">
                    <p class="purchase__block-title">支払い方法</p>

                    <div class="cselect" data-name="payment_method">
                        <input type="hidden" name="payment_method" form="purchaseForm" value="{{ session('purchase_payment_method','') }}">
                        <button type="button"
                            class="cselect__button"
                            aria-haspopup="listbox"
                            aria-expanded="false">
                            <span class="cselect__text" id="paymentMethodText">
                                {{ session('purchase_payment_method_label', '選択してください') }}
                            </span>
                            <span class="cselect__chev">▾</span>
                        </button>

                        <ul class="cselect__list" role="listbox">
                            <li class="cselect__option"
                                role="option"
                                data-value="convenience"
                                aria-selected="false">
                                <span class="cselect__check">✓</span>
                                <span class="cselect__option-text">コンビニ支払い</span>
                            </li>
                            <li class="cselect__option"
                                role="option"
                                data-value="card"
                                aria-selected="false">
                                <span class="cselect__check">✓</span>
                                <span class="cselect__option-text">カード支払い</span>
                            </li>
                        </ul>
                    </div>

                    @error('payment_method')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="purchase__block">
                    <div class="purchase__address-head">
                        <p class="purchase__block-title">配送先</p>
                        <a href="{{ route('purchase.address.edit', $item->id) }}" class="purchase__address-link">
                            変更する
                        </a>
                    </div>

                    <div class="purchase__address">
                        <p>〒 {{ $shipping['postal_code'] ?? 'XXX-YYYY' }}</p>
                        <p>{{ $shipping['address'] ?? 'ここには住所と建物が入ります' }}</p>
                        @if (!empty($shipping['building_name']))
                        <p>{{ $shipping['building_name'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <aside class="purchase__summary">
            <div class="purchase__summary-box">
                <div class="purchase__summary-row">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </div>

                <div class="purchase__summary-row">
                    <span>支払い方法</span>
                    <span id="summaryPaymentMethodRight">
                        {{ session('purchase_payment_method_label', '選択してください') }}
                    </span>
                </div>
            </div>

            <form id="purchaseForm" method="POST" action="{{ route('purchase.store', $item->id) }}">
                @csrf
                <button type="submit" class="purchase__button">購入する</button>
            </form>

            <form id="stripeForm" method="POST" action="{{ route('checkout.create', $item->id) }}" style="display:none;">
                @csrf
                <input type="hidden" name="payment_method" value="card">
            </form>
        </aside>
    </div>
</section>

<script>
    document.addEventListener('click', (e) => {
        const submitBtn = e.target.closest('#purchaseForm button[type="submit"]');
        if (!submitBtn) return;

        const pm = document.querySelector('input[name="payment_method"][form="purchaseForm"]');
        if (pm && pm.value === 'card') {
            e.preventDefault();
            document.getElementById('stripeForm').submit();
        }
    });
</script>
@endsection