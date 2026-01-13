@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endpush

@section('content')
<div class="mypage-container">
    <div class="mypage-header">
        <div class="mypage-user">
            <div class="mypage-user__image"></div>
            <div class="mypage-user__name">{{ $user->name }}</div>
        </div>

        <div class="mypage-edit">
            <a href="/mypage/profile">プロフィールを編集</a>
        </div>
    </div>

    <div class="mypage-tabs">
        <a href="/mypage?page=sell">出品した商品</a>
        <a href="/mypage?page=buy">購入した商品</a>
    </div>
</div>
@endsection