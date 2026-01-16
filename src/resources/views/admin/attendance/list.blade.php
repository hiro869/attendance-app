@extends('layouts.admin')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/admin/list.css') }}">
@endpush

@section('content')
<div class="admin-attendance-wrapper">

    {{-- タイトル --}}
    <div class="admin-title">
        <span class="bar"></span>
        {{ $dateLabel }}
    </div>

    {{-- 日付ナビ（Figmaと同じ見た目） --}}
    <div class="date-nav-bar">
        <a href="{{ route('admin.attendance.list',['date'=>$prevDate]) }}" class="nav-left">
            ← 前日
        </a>

        {{-- ★ ここが変更点 --}}
        <div class="nav-center">
            <span class="calendar-icon">📅</span>
            <span class="nav-date">
                {{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}
            </span>

            {{-- 機能保持用（非表示） --}}
            <form method="GET" action="{{ route('admin.attendance.list') }}">
                <input type="date" name="date" value="{{ $date }}" class="hidden-date">
            </form>
        </div>

        <a href="{{ route('admin.attendance.list',['date'=>$nextDate]) }}" class="nav-right">
            翌日 →
        </a>
    </div>

    {{-- テーブル --}}
    <table class="admin-attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $att)
            <tr>
                <td>{{ $att->name }}</td>
                <td>{{ $att->start }}</td>
                <td>{{ $att->end }}</td>
                <td>{{ $att->break }}</td>
                <td>{{ $att->total }}</td>
                <td>
                    <a href="{{ route('admin.attendance.detail', $att->id) }}" class="detail-btn">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
