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
                <img src="/images/logo.png" alt="logo" class="logo">

            {{-- ▼ ログイン前 --}}
            @guest
                <nav class="nav"></nav>
            @endguest

            {{-- ▼ ログイン後 --}}
            @auth
    @if (!request()->routeIs('verification.notice'))

        @php
            $attendance = \App\Models\Attendance::where('user_id', auth()->id())
                        ->where('work_date', \Carbon\Carbon::today()->toDateString())
                        ->first();

            $status = $attendance->status ?? 0;
        @endphp

        <nav class="nav">

            {{-- ★① 出勤前 --}}
            @if ($status === 0)
                <a href="{{ url('/attendance') }}">勤怠</a>
                <a href="{{ url('/attendance/list') }}">勤怠一覧</a>
                <a href="{{ route('request.index') }}">申請</a>

            {{-- ★② 出勤中・休憩中 --}}
            @elseif ($status === 1 || $status === 2)
                <a href="{{ url('/attendance') }}">勤怠</a>
                <a href="{{ url('/attendance/list') }}">勤怠一覧</a>
                <a href="{{ route('request.index') }}">申請</a>

            {{-- ★③ 退勤済 --}}
            @elseif ($status === 3)
                <a href="{{ url('/attendance/list') }}">今月の出勤一覧</a>
                <a href="{{ route('request.index') }}">申請一覧</a>
            @endif

            {{-- ログアウト --}}
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit">ログアウト</button>
            </form>

        </nav>
    @endif
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
