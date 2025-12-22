@extends('layouts.app')
@push('page_css')
<link rel="stylesheet" href="{{ asset('css/attendance/index.css') }}">
@endpush

@section('content')
<div class="attendance-wrapper">

    <p class="status">
        @if (!$attendance)
            勤務外
        @elseif ($attendance->status === 1)
            出勤中
        @elseif ($attendance->status === 2)
            休憩中
        @elseif ($attendance->status === 3)
            退勤済
        @endif
    </p>

    <p class="date">{{ \Carbon\Carbon::now()->isoFormat('YYYY年M月D日(ddd)') }}</p>
    <p class="time">{{ \Carbon\Carbon::now()->format('H:i') }}</p>

    <form action="{{ route('attendance.store') }}" method="POST">
        @csrf

        @if (!$attendance || $attendance->status === 0)
            <button name="action" value="start" class="btn-main">出勤</button>
        @endif

        @if ($attendance && $attendance->status === 1)
            <button name="action" value="break_start" class="btn-sub">休憩入</button>
            <button name="action" value="end" class="btn-main">退勤</button>
        @endif

        @if ($attendance && $attendance->status === 2)
            <button name="action" value="break_end" class="btn-sub">休憩戻</button>
        @endif

        @if ($attendance && $attendance->status === 3)
            <p>お疲れ様でした。</p>
        @endif
    </form>

</div>
@endsection
