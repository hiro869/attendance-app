@extends('layouts.app')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endpush

@section('content')
<div class="auth-box">
    <h1 class="auth-title">会員登録</h1>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <label>名前</label>
        <input type="text" name="name" value="{{ old('name') }}">
        @error('name')
            <p class="error">{{ $message }}</p>
        @enderror

        <label>メールアドレス</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror

        <label>パスワード</label>
        <input type="password" name="password">
        @error('password')
            <p class="error">{{ $message }}</p>
        @enderror

        <label>パスワード確認</label>
        <input type="password" name="password_confirmation">
        @error('password_confirmation')
            <p class="error">{{ $message }}</p>
        @enderror

        <button class="btn">登録する</button>

        <p class="link"><a href="{{ route('login') }}">ログインはこちら</a></p>
    </form>
</div>
@endsection
