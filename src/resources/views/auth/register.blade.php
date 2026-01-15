@extends('layouts.app')

@section('title', '会員登録')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush

@section('content')
<div class="register-container">
    <h1>会員登録</h1>
    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <div>
            <label for="name">ユーザー名</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required>
            @error('name')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="email">メールアドレス</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required>
            @error('email')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="password">パスワード</label>
            <input
                id="password"
                type="password"
                name="password"
                required>
            @error('password')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="password_confirmation">確認用パスワード</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required>
            @error('password_confirmation')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit">登録する</button>
        <a href="{{ route('login') }}">ログインはこちら</a>
    </form>
</div>
@endsection