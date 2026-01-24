@extends('layouts.app')

@section('title', '出品内容の確認')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endpush

@section('content')
<div class="sell-container">
    <h1 class="sell-title">出品内容の確認</h1>

    {{-- 商品画像 --}}
    <div class="sell-section">
        <label class="sell-label">商品画像</label>

        @if (!empty($publicPath))
        <div class="sell-image-box">
            <img
                class="sell-image-preview"
                src="{{ asset($publicPath) }}"
                alt="出品画像"
                style="display:block;">
        </div>
        @endif
    </div>

    {{-- 商品の詳細 --}}
    <div class="sell-section">
        <h2 class="sell-subtitle">商品の詳細</h2>

        @php
        $categoryNames = \Illuminate\Support\Facades\DB::table('categories')
        ->whereIn('id', $data['category_ids'] ?? [])
        ->orderBy('id')
        ->pluck('name')
        ->toArray();

        $conditionName = \Illuminate\Support\Facades\DB::table('conditions')
        ->where('id', $data['condition'] ?? null)
        ->value('name');
        @endphp

        <label class="sell-label">カテゴリー</label>

        @if (!empty($categoryNames))
        <div class="sell-categories">
            @foreach ($categoryNames as $name)
            <span class="category-check">
                <span class="category">{{ $name }}</span>
            </span>
            @endforeach
        </div>
        @else
        <p>―</p>
        @endif


        <label class="sell-label">商品の状態</label>
        <p>{{ $conditionName ?? '―' }}</p>
    </div>

    {{-- 商品名と説明 --}}
    <div class="sell-section">
        <h2 class="sell-subtitle">商品名と説明</h2>

        <label class="sell-label">商品名</label>
        <p>{{ $data['name'] }}</p>

        <label class="sell-label">ブランド名</label>
        <p>{{ $data['brand'] ?? '―' }}</p>

        <label class="sell-label">商品の説明</label>
        <p style="white-space: pre-wrap;">{{ $data['description'] }}</p>

        <label class="sell-label">販売価格</label>
        <p>¥{{ number_format($data['price']) }}</p>
    </div>

    {{-- 戻る --}}
    <form method="POST" action="{{ route('sell.back') }}">
        @csrf
        <input type="hidden" name="name" value="{{ $data['name'] }}">
        <input type="hidden" name="brand" value="{{ $data['brand'] ?? '' }}">
        <input type="hidden" name="description" value="{{ $data['description'] }}">
        <input type="hidden" name="price" value="{{ $data['price'] }}">
        <input type="hidden" name="condition" value="{{ $data['condition'] }}">

        @foreach(($data['category_ids'] ?? []) as $cid)
        <input type="hidden" name="category_ids[]" value="{{ $cid }}">
        @endforeach

        <button type="submit" class="sell-submit">戻る</button>
    </form>

    {{-- OK（確定） --}}
    <form method="POST" action="{{ url('/sell') }}" style="margin-top:12px;">
        @csrf
        <input type="hidden" name="name" value="{{ $data['name'] }}">
        <input type="hidden" name="brand" value="{{ $data['brand'] ?? '' }}">
        <input type="hidden" name="description" value="{{ $data['description'] }}">
        <input type="hidden" name="price" value="{{ $data['price'] }}">
        <input type="hidden" name="condition" value="{{ $data['condition'] }}">

        @foreach(($data['category_ids'] ?? []) as $cid)
        <input type="hidden" name="category_ids[]" value="{{ $cid }}">
        @endforeach

        <button type="submit" class="sell-submit">この内容で出品する</button>
    </form>
</div>
@endsection