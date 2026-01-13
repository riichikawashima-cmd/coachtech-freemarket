@extends('layouts.app')

@section('title', 'ログイン')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="login-container">
    <h1 class="login-title">ログイン</h1>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="form-group">
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

        <div class="form-group">
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

        <button type="submit" class="login-button">ログインする</button>
    </form>

    <a class="register-link" href="{{ route('register') }}">会員登録はこちら</a>
</div>
@endsection