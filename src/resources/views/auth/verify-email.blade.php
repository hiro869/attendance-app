@extends('layouts.app')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endpush

@section('content')
<div class="verify-page">
    <div class="verify-wrapper">

        <p class="verify-message">
            登録していただいたメールアドレスに認証メールを送信しました。<br>
            メール認証を完了してください。
        </p>

        {{-- ▼ 認証はこちらから（Mailhogへ） --}}
        <a href="http://localhost:8025" target="_blank" class="verify-button">
            認証はこちらから
        </a>

        {{-- ▼ 認証メール再送 --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="resend-link">
                認証メールを再送する
            </button>
        </form>

    </div>
</div>
@endsection
