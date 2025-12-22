@extends('layouts.admin')

@section('content')

<div class="login-wrapper">

    <h2 class="title">管理者ログイン</h2>

    <form action="{{ route('login') }}" method="POST" class="login-form">
        @csrf

        <label>メールアドレス</label>
        <input type="email" name="email">

        <label>パスワード</label>
        <input type="password" name="password">

        <button type="submit" class="login-btn">
            管理者ログインする
        </button>
    </form>

</div>

@endsection
