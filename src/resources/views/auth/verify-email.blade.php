@extends('layouts.app')

@section('title', 'メール認証')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="verify-wrap">
    <p class="verify-text">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <a href="http://localhost:8025" class="verify-btn" target="_blank" rel="noopener">
        認証はこちらから
    </a>

    <form method="POST" action="{{ route('verification.send') }}" class="verify-resend">
        @csrf
        <button type="submit" class="verify-link">認証メールを再送する</button>
    </form>
</div>
@endsection