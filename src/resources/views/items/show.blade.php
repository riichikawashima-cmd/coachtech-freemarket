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

    <div class="item-show__info">
        <h1 class="item-title">{{ $item->name }}</h1>
        <p class="item-brand">{{ $item->brand }}</p>
        <p class="item-price">
            ¥{{ number_format($item->price) }}
            <span class="item-tax">（税込）</span>
        </p>
        <div class="item-actions">
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
            <form action="{{ route('login') }}" method="GET" class="icon-form">
                <button type="submit" class="icon-button">
                    <img src="{{ asset('images/heart-default.png') }}" alt="like" class="icon">
                    <span class="icon-count">{{ $item->likes_count }}</span>
                </button>
            </form>
            @endguest

            <div class="icon-button is-static">
                <img src="{{ asset('images/comment.png') }}" alt="comment" class="icon">
                <span class="icon-count">{{ $item->comments_count }}</span>
            </div>
        </div>

        @auth
        @if ($item->user_id === auth()->id())
        <span class="purchase-button is-disabled">購入手続きへ</span>
        @else
        <a href="{{ url('/purchase/' . $item->id) }}" class="purchase-button">購入手続きへ</a>
        @endif
        @endauth

        @guest
        <a href="{{ route('login') }}" class="purchase-button">購入手続きへ</a>
        @endguest

        <h2 class="section-title">商品説明</h2>
        @if (!empty($item->description))
        <p class="item-description">{{ $item->description }}</p>
        @endif

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

        <h2 class="section-title">コメント（{{ $item->comments_count }}）</h2>
        <div class="comment-list">
            @forelse ($comments as $comment)
            <div class="comment-card">
                <div class="comment-avatar">
                    @if (!empty($comment->image_path))
                    <img src="{{ asset('storage/' . $comment->image_path) }}" alt="avatar">
                    @else
                    <img alt="avatar" style="visibility:hidden;">
                    @endif
                </div>

                <div class="comment-main">
                    <div class="comment-meta">
                        <span class="comment-name">{{ $comment->user_name }}</span>
                    </div>
                </div>

                <div class="comment-text">{{ $comment->comment }}</div>
            </div>
            @empty
            <p class="comment-empty">コメントはまだありません。</p>
            @endforelse
        </div>

        @if ($comments->hasPages())
        <nav class="simple-pagination">
            @if ($comments->onFirstPage())
            <span class="simple-pagination__btn is-disabled">&laquo;</span>
            @else
            <a href="{{ $comments->previousPageUrl() }}" class="simple-pagination__btn">&laquo;</a>
            @endif

            @foreach ($comments->getUrlRange(1, $comments->lastPage()) as $page => $url)
            @if ($page == $comments->currentPage())
            <span class="simple-pagination__page is-current">{{ $page }}</span>
            @else
            <a href="{{ $url }}" class="simple-pagination__page">{{ $page }}</a>
            @endif
            @endforeach

            @if ($comments->hasMorePages())
            <a href="{{ $comments->nextPageUrl() }}" class="simple-pagination__btn">&raquo;</a>
            @else
            <span class="simple-pagination__btn is-disabled">&raquo;</span>
            @endif
        </nav>
        @endif

        <h2 class="section-title">商品へのコメント</h2>

        @auth
        <form method="POST" action="{{ route('comment.store', $item->id) }}" class="comment-form">
            @csrf
            <textarea name="comment">{{ old('comment') }}</textarea>

            @error('comment')
            <p class="error-text">{{ $message }}</p>
            @enderror

            <button type="submit">コメントを送信する</button>
        </form>
        @endauth

        @guest
        <form action="{{ route('login') }}" method="GET" class="comment-form">
            <textarea disabled></textarea>
            <button type="submit">コメントを送信する</button>
        </form>
        @endguest

    </div>
</section>
@endsection