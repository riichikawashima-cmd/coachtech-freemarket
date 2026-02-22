@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endpush

@section('content')
@php
$page = request('page', 'sell'); // sell | buy
@endphp

<div class="mypage-container">
    <div class="mypage-header">
        <div class="mypage-user">
            <div class="mypage-user__image">
                @if (!empty($profile?->image_path))
                <img src="{{ Storage::url($profile->image_path) }}" alt="">
                @else
                <img alt="avatar" style="visibility:hidden;">
                @endif
            </div>
            <div class="mypage-user__name">{{ $user->name }}</div>
        </div>
        <div class="mypage-edit">
            <a href="/mypage/profile">プロフィールを編集</a>
        </div>
    </div>

    <div class="mypage-tabs">
        <div class="mypage-tabs__inner">
            <a href="/mypage?page=sell" class="{{ $page === 'sell' ? 'is-active' : '' }}">出品した商品</a>
            <a href="/mypage?page=buy" class="{{ $page === 'buy' ? 'is-active' : '' }}">購入した商品</a>
        </div>
    </div>

    {{-- 一覧（indexと同じ構造・class） --}}
    <div class="mypage-items">
        @if ($page === 'sell')
        @forelse ($sellItems as $item)
        <a href="{{ route('item.show', $item->id) }}" class="item-card">
            <div class="item-image">
                <img src="{{ $item->image_path }}" alt="{{ $item->name }}">
            </div>
            <div class="item-name">{{ $item->name }}</div>
        </a>
        @empty
        <p class="mypage-empty">出品した商品がありません。</p>
        @endforelse
        @else
        @forelse ($buyItems as $item)
        <a href="{{ route('item.show', $item->id) }}" class="item-card">
            <div class="item-image">
                <img src="{{ $item->image_path }}" alt="{{ $item->name }}">
            </div>
            <div class="item-name">{{ $item->name }}</div>
        </a>
        @empty
        <p class="mypage-empty">購入した商品がありません。</p>
        @endforelse
        @endif
    </div>
</div>
@endsection