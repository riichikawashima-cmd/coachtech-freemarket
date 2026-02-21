@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('title', '配送先住所変更')

@section('content')
<section class="address-edit">
    <h1 class="address-edit__title">住所の変更</h1>

    <form method="POST" action="{{ route('purchase.address.update', $item_id) }}" class="address-edit__form">
        @csrf

        <div class="address-edit__field">
            <label class="address-edit__label">郵便番号</label>
            <input
                type="text"
                name="postal_code"
                class="address-edit__input"
                value="{{ old('postal_code', $profile->postal_code ?? '') }}">

            @error('postal_code')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="address-edit__field">
            <label class="address-edit__label">住所</label>
            <input
                type="text"
                name="address"
                class="address-edit__input"
                value="{{ old('address', $profile->address ?? '') }}">

            @error('address')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="address-edit__field">
            <label class="address-edit__label">建物名</label>
            <input
                type="text"
                name="building_name"
                class="address-edit__input"
                value="{{ old('building_name', $profile->building_name ?? '') }}">

            @error('building_name')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="address-edit__button">
            更新する
        </button>
    </form>
</section>
@endsection