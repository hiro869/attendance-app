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

    {{-- 🔥ここが一体化した白バー --}}
    <div class="date-nav-bar">
        <a href="{{ route('admin.attendance.list',['date'=>$prevDate]) }}" class="nav-left">← 前日</a>

        <form method="GET" action="{{ route('admin.attendance.list') }}" class="nav-center">
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()">
        </form>

        <a href="{{ route('admin.attendance.list',['date'=>$nextDate]) }}" class="nav-right">翌日 →</a>
    </div>

    {{-- テーブル --}}
    <table class="admin-attendance-table">
        <thead>
            <tr>
                <th>名前</th><th>出勤</th><th>退勤</th><th>休憩</th><th>合計</th><th>詳細</th>
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
                <td><a href="{{ route('admin.attendance.detail', $att->id) }}" class="detail-btn">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
