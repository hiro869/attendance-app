@extends('layouts.admin')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/request/approve.css') }}">
@endpush

@section('content')
<div class="detail-page">
    <div class="detail-wrapper">

        <div class="detail-title">
            <span class="title-bar"></span>
            <h2>勤怠詳細</h2>
        </div>

        {{-- カード --}}
        <div class="detail-card">
            <table class="detail-table">

                <tr>
                    <th>名前</th>
                    <td>{{ $correction->user->name }}</td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td class="date-row">
                        <span>{{ $correction->attendance->work_date->format('Y年') }}</span>
                        <span>{{ $correction->attendance->work_date->format('n月j日') }}</span>
                    </td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-row">
                        <span>{{ optional($correction->request_start_time)->format('H:i') }}</span>
                        <span>〜</span>
                        <span>{{ optional($correction->request_end_time)->format('H:i') }}</span>
                    </td>
                </tr>

                @foreach ($correction->request_breaks ?? [] as $i => $break)
                <tr>
                    <th>休憩{{ $i + 1 }}</th>
                    <td class="time-row">
                        <span>{{ \Carbon\Carbon::parse($break['start'])->format('H:i') }}</span>
                        <span>〜</span>
                        <span>{{ \Carbon\Carbon::parse($break['end'])->format('H:i') }}</span>
                    </td>
                </tr>
                @endforeach

                <tr>
                    <th>休憩{{ count($correction->request_breaks ?? []) + 1 }}</th>
                    <td></td>
                </tr>

                <tr>
                    <th>備考</th>
                    <td>{{ $correction->note }}</td>
                </tr>

            </table>
        </div>

        {{-- ボタン（カード外・右） --}}
        <div class="action-area">
            @if ($correction->status === 0)
                <form method="POST">
                    @csrf
                    <button class="approve-btn">承認</button>
                </form>
            @else
                <span class="approved-btn">承認済み</span>
            @endif
        </div>

    </div>
</div>
@endsection
