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

            {{-- ▼ ログイン前 --}}
            @guest
                <nav class="nav"></nav>
            @endguest

            {{-- ▼ ログイン後 --}}
            @auth
                @php
                    // 今日の勤怠情報
                    $attendance = \App\Models\Attendance::where('user_id', auth()->id())
                                ->where('work_date', \Carbon\Carbon::today()->toDateString())
                                ->first();

                    // status が null の場合は 0（勤務外）として扱う
                    $status = $attendance->status ?? 0;
                @endphp

                <nav class="nav">

                    {{-- ★① 出勤前（勤務外 0） --}}
                    @if ($status === 0)
                        <a href="{{ route('attendance.index') }}">勤怠</a>
                        <a href="{{ route('attendance.list') }}">勤怠一覧</a>
                        <a href="{{ route('request.index') }}">申請</a>

                    {{-- ★② 出勤中（1）・休憩中（2） --}}
                    @elseif ($status === 1 || $status === 2)
                        <a href="{{ route('attendance.index') }}">勤怠</a>
                        <a href="{{ route('attendance.list') }}">勤怠一覧</a>
                        <a href="{{ route('request.index') }}">申請</a>

                    {{-- ★③ 退勤済（3） --}}
                    @elseif ($status === 3)
                        <a href="{{ route('attendance.list') }}">今月の出勤一覧</a>
                        <a href="{{ route('request.index') }}">申請一覧</a>
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

    {{-- ▼ メイン --}}
    <main class="main">
        @yield('content')
    </main>

</body>
</html>
