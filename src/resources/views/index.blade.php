@extends('layouts.app')

@section('title', '商品一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush

@section('content')
@php
$tab = request('tab', 'recommend');
$keyword = request('keyword', '');
@endphp

<div class="items-page">

    <div class="items-tabs">
        <a href="{{ url('/?tab=recommend&keyword=' . urlencode($keyword)) }}"
            class="items-tab {{ $tab === 'recommend' ? 'is-active' : '' }}">
            おすすめ
        </a>

        <a href="{{ url('/?tab=mylist&keyword=' . urlencode($keyword)) }}"
            class="items-tab {{ $tab === 'mylist' ? 'is-active' : '' }}">
            マイリスト
        </a>
    </div>

    <div class="items-divider"></div>

    <div class="items-grid">
        @forelse ($items as $item)
        <a href="{{ url('/item/' . $item->id) }}" class="item-card">
            <div class="item-image">
                @if ($item->is_sold)
                <span class="item-sold">Sold</span>
                @endif
                @if (!empty($item->image_path))
                <img src="{{ $item->image_path }}" alt="{{ $item->name }}">
                @endif
            </div>
            <div class="item-name">{{ $item->name }}</div>
        </a>
        @empty
        <p>商品がありません</p>
        @endforelse
    </div>

</div>
@endsection