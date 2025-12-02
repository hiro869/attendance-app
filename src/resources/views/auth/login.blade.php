@extends('layouts.app')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endpush

@section('content')
<div class="auth-box">
    <h1 class="auth-title">ログイン</h1>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <label>メールアドレス</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email') <p class="error">{{ $message }}</p> @enderror

        <label>パスワード</label>
        <input type="password" name="password">
        @error('password') <p class="error">{{ $message }}</p> @enderror

        <button class="btn">ログインする</button>

        <p class="link"><a href="{{ route('register') }}">会員登録はこちら</a></p>
    </form>
</div>
@endsection
