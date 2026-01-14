@extends('layouts.admin')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endpush

@section('content')
<div class="detail-page">
    <div class="detail-wrapper">

        {{-- タイトル --}}
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
                    <td>{{ $correction->attendance->work_date->format('Y年m月d日') }}</td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-row">
                        {{ optional($correction->request_start_time)->format('H:i') }}
                        〜
                        {{ optional($correction->request_end_time)->format('H:i') }}
                    </td>
                </tr>

                <tr>
                    <th>休憩</th>
                    <td class="time-row">
                        @if($correction->request_breaks)
                            {{ \Carbon\Carbon::parse($correction->request_breaks[0]['start'])->format('H:i') }}
                            〜
                            {{ \Carbon\Carbon::parse($correction->request_breaks[0]['end'])->format('H:i') }}
                        @else
                            ー
                        @endif
                    </td>
                </tr>

                <tr>
                    <th>備考</th>
                    <td>{{ $correction->note }}</td>
                </tr>

            </table>

            {{-- 承認ボタン --}}
            @if ($correction->status === 0)
                <form method="POST" class="btn-area">
                    @csrf
                    <button type="submit" class="edit-btn">承認</button>
                </form>
            @else
                <div class="btn-area">
                    <p class="approved">承認済み</p>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
