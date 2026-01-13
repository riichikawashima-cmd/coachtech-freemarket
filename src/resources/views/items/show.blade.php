@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endpush

@section('title', '商品詳細')

@section('content')
<section class="item-show">
    {{-- 左：商品画像 --}}
    <div class="item-show__image">
        <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}">
    </div>

    {{-- 右：商品情報 --}}
    <div class="item-show__info">
        <h1 class="item-title">{{ $item->name }}</h1>
        <p class="item-brand">{{ $item->brand }}</p>

        <p class="item-price">
            ¥{{ number_format($item->price) }}
            <span class="item-tax">（税込）</span>
        </p>

        {{-- いいね・コメント（アイコンの下に数字） --}}
        <div class="item-actions">
            {{-- いいね --}}
            @auth
            @if ($isLiked)
            <form action="{{ route('like.destroy', $item->id) }}" method="POST" class="icon-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="icon-button">
                    <img src="{{ asset('images/heart-liked.png') }}" alt="liked" class="icon">
                    <span class="icon-count">{{ $item->likes_count }}</span>
                </button>
            </form>
            @else
            <form action="{{ route('like.store', $item->id) }}" method="POST" class="icon-form">
                @csrf
                <button type="submit" class="icon-button">
                    <img src="{{ asset('images/heart-default.png') }}" alt="like" class="icon">
                    <span class="icon-count">{{ $item->likes_count }}</span>
                </button>
            </form>
            @endif
            @endauth

            @guest
            <div class="icon-button is-static">
                <img src="{{ asset('images/heart-default.png') }}" alt="like" class="icon">
                <span class="icon-count">{{ $item->likes_count }}</span>
            </div>
            @endguest

            {{-- コメント --}}
            <div class="icon-button is-static">
                <img src="{{ asset('images/comment.png') }}" alt="comment" class="icon">
                <span class="icon-count">{{ $item->comments_count }}</span>
            </div>
        </div>

        {{-- 購入ボタン --}}
        <a href="{{ url('/purchase/' . $item->id) }}" class="purchase-button">購入手続きへ</a>

        {{-- 商品説明 --}}
        <h2 class="section-title">商品説明</h2>
        @if (!empty($item->description))
        <p class="item-description">{{ $item->description }}</p>
        @endif

        {{-- 商品の情報 --}}
        <h2 class="section-title">商品の情報</h2>
        <div class="item-info">
            <div class="item-info-row">
                <div class="item-info-label">カテゴリー</div>
                <div class="item-info-value">
                    <ul class="item-categories">
                        @foreach ($categories as $category)
                        <li class="item-category">{{ $category }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="item-info-row">
                <div class="item-info-label">商品の状態</div>
                <div class="item-info-value">{{ $item->condition }}</div>
            </div>
        </div>

        {{-- コメント --}}
        <h2 class="section-title">コメント（{{ $item->comments_count }}）</h2>

        <div class="comment-list">
            @forelse ($comments as $comment)
            <div class="comment-card">
                <div class="comment-avatar"></div>

                <div class="comment-main">
                    <div class="comment-meta">
                        <span class="comment-name">{{ $comment->user_name }}</span>
                        <span class="comment-time">{{ $comment->created_at }}</span>
                    </div>
                    <div class="comment-text">{{ $comment->comment }}</div>
                </div>
            </div>
            @empty
            <p class="comment-empty">コメントはまだありません。</p>
            @endforelse
        </div>

        {{-- 商品へのコメント --}}
        <h2 class="section-title">商品へのコメント</h2>

        @auth
        <form method="POST" action="{{ route('comment.store', $item->id) }}" class="comment-form">
            @csrf
            <textarea name="comment" placeholder="コメントを書く"></textarea>
            @error('comment')
            <p class="form-error">{{ $message }}</p>
            @enderror
            <button type="submit">コメントを送信する</button>
        </form>
        @endauth
    </div>
</section>
@endsection