@extends('layouts.app')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
@endpush

@section('content')
<div class="attendance-wrapper">

    <h2 class="page-title">
        <span class="title-bar"></span>
        勤怠一覧
    </h2>

    <div class="month-switch">
        <a href="{{ route('attendance.list', ['month' => $prev]) }}" class="switch-btn">← 前月</a>

        <div class="current-month">
            <span class="calendar-icon">📅</span>
            {{ $current->format('Y年n月') }}
        </div>

        <a href="{{ route('attendance.list', ['month' => $next]) }}" class="switch-btn">翌月 →</a>
    </div>

    <div class="table-box">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $row)
                    <tr>
                        <td>{{ $row['date'] }}</td>
                        <td>{{ $row['start'] }}</td>
                        <td>{{ $row['end'] }}</td>
                        <td>{{ $row['break'] }}</td>
                        <td>{{ $row['total'] }}</td>
                        <td>
                            @if($row['id'])
                                <a href="{{ route('attendance.detail', $row['id']) }}">詳細</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
