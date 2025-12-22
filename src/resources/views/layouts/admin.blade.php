<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理画面</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('page_css')
</head>

<body>

<header class="header">
    <div class="header-inner">

        {{-- 全画面共通ロゴ --}}
        <a href="#">
            <img src="/images/logo.png" class="logo">
        </a>

        {{-- ★ログイン画面以外でメニュー表示 --}}
        @if(!Route::is('admin.login'))
            <nav class="admin-nav">
                <a href="{{ route('admin.attendance.index') }}">勤怠一覧</a>
                <a href="{{ route('admin.staff.index') }}">スタッフ一覧</a>
                <a href="{{ route('admin.request.index') }}">申請一覧</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button>ログアウト</button>
                </form>
            </nav>
        @endif

    </div>
</header>

<main class="main">
    @yield('content')
</main>

</body>
</html>
