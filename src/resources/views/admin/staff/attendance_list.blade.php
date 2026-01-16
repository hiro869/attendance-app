@extends('layouts.admin')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/admin/month_attendance.css') }}">
@endpush

@section('content')
<div class="attendance-wrapper">

    {{-- タイトル --}}
    <h2 class="page-title">
        <span class="title-bar"></span>
        {{ $staff->name }}さんの勤怠
    </h2>

    {{-- 月切り替え --}}
    <div class="month-switch">
        <a class="switch-btn"
           href="{{ route('admin.staff.attendance', [
               'id'=>$staff->id,
               'month'=>$prev
           ]) }}">
            ← 前月
        </a>

        <div class="current-month">
            <span class="calendar-icon">📅</span>
            {{ $current->format('Y/m') }}
        </div>

        <a class="switch-btn"
           href="{{ route('admin.staff.attendance', [
               'id'=>$staff->id,
               'month'=>$next
           ]) }}">
            翌月 →
        </a>
    </div>

    {{-- 勤怠テーブル --}}
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
                @foreach ($rows as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['start'] !== 'ー' ? $row['start'] : ' ' }}</td>
                    <td>{{ $row['end']   !== 'ー' ? $row['end']   : ' ' }}</td>
                    <td>{{ $row['break'] !== 'ー' ? $row['break'] : ' ' }}</td>
                    <td>{{ $row['total'] !== 'ー' ? $row['total'] : ' ' }}</td>
                    <td>
                        <a class="detail-link"
                           href="{{ route('admin.attendance.detail.byDate', [
                               'user'=>$staff->id,
                               'date'=>$row['raw_date']
                           ]) }}">
                            詳細
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ✅ CSV出力ボタン --}}
    <div class="csv-wrapper">
        <a
            href="{{ route('admin.staff.attendance.csv', [
                'id'    => $staff->id,
                'month' => $current->format('Y-m')
            ]) }}"
            class="csv-button"
        >
            CSV出力
        </a>
    </div>

</div>
@endsection
