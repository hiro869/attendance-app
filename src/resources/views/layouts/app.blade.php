<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Attendance') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('page_css')
</head>

<body>

    {{-- ▼▼ ヘッダー ▼▼ --}}
    <header class="header">
        <div class="header-inner">

            {{-- ロゴ --}}
            <a href="/">
                <img src="/images/logo.png" alt="logo" class="logo">
            </a>

            {{-- ▼ ログイン前のメニュー --}}
            @guest
                <nav class="nav"></nav>
            @endguest

            {{-- ▼ ログイン後のメニュー --}}
            @auth
                @php
                    $attendance = \App\Models\Attendance::where('user_id', auth()->id())
                                ->where('work_date', \Carbon\Carbon::today()->toDateString())
                                ->first();
                @endphp

                <nav class="nav">

                    {{-- ★ ① 出勤前（勤務外） --}}
                    @if (!$attendance || $attendance->status === 0)
                        <a href="{{ route('attendance.index') }}">勤怠</a>

                    {{-- ★ ② 出勤中・休憩中 --}}
                    @elseif ($attendance->status === 1 || $attendance->status === 2)
                        <a href="{{ route('attendance.index') }}">勤怠</a>
                        <a href="{{ route('attendance.list') }}">勤怠一覧</a>
                        <a href="{{ route('application.index') }}">申請</a>

                    {{-- ★ ③ 退勤済 --}}
                    @elseif ($attendance->status === 3)
                        <a href="{{ route('attendance.month') }}">今月の出勤一覧</a>
                        <a href="{{ route('application.index') }}">申請一覧</a>
                    @endif

                    {{-- ログアウト --}}
                    <form action="{{ route('logout') }}" method="POST" class="logout-form">
                        @csrf
                        <button type="submit">ログアウト</button>
                    </form>
                </nav>

            @endauth

        </div>
    </header>
    {{-- ▲▲ ヘッダー ▲▲ --}}

    {{-- メインエリア --}}
    <main class="main">
        @yield('content')
    </main>

</body>
</html>
