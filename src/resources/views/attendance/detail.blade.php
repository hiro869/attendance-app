@extends('layouts.app')

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

        {{-- 勤怠カード --}}
        <div class="detail-card">

            @php
                $hasRequest = !is_null($correctionRequest);
                $break = $breaks->first();
            @endphp

            {{-- 修正申請がない場合のみ form --}}
            @if (!$hasRequest)
            <form method="POST" action="{{ route('attendance.requestCorrection', $attendance->id) }}">
                @csrf

            @endif
            <table class="detail-table">

                {{-- 名前 --}}
                <tr>
                    <th>名前</th>
                    <td>{{ auth()->user()->name }}</td>
                </tr>

                {{-- 日付 --}}
                <tr>
                    <th>日付</th>
                    <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年m月d日') }}</td>
                </tr>

                {{-- 出勤退勤 --}}
                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-row">

                        {{-- 承認待ちなら元データ表示 --}}
                        @if ($hasRequest)
                            <span>{{ optional($attendance->start_time)->format('H:i') }}</span>
                            〜
                            <span>{{ optional($attendance->end_time)->format('H:i') }}</span>

                        {{-- 修正フォーム --}}
                        @else
                            <input type="time" name="start_time"
                                value="{{ old('start_time', optional($attendance->start_time)->format('H:i')) }}">
                            〜
                            <input type="time" name="end_time"
                                value="{{ old('end_time', optional($attendance->end_time)->format('H:i')) }}">

                            @error('start_time')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                            @error('end_time')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                            @error('time')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        @endif

                    </td>
                </tr>

                {{-- 休憩 --}}
                <tr>
                    <th>休憩</th>
                    <td class="time-row">

                        @if ($hasRequest)
                            <span>{{ optional($break)->break_start?->format('H:i') }}</span>
                            〜
                            <span>{{ optional($break)->break_end?->format('H:i') }}</span>

                        @else
                            <input type="time" name="break_start"
                                value="{{ old('break_start', optional($break)->break_start?->format('H:i')) }}">
                            〜
                            <input type="time" name="break_end"
                                value="{{ old('break_end', optional($break)->break_end?->format('H:i')) }}">

                            @error('break_start')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                            @error('break_end')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                            @error('break')
                                <p class="error-text">{{ $message }}</p>
                            @enderror

                        @endif

                    </td>
                </tr>

                {{-- 備考 --}}
                <tr>
                    <th>備考</th>
                    <td>

                        @if ($hasRequest)
                            {{ $correctionRequest->note }}

                        @else
                            <input type="text" name="note"
                                   placeholder="修正理由を入力してください"
                                   value="{{ old('note') }}">

                            @error('note')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        @endif

                    </td>
                </tr>

            </table>

            {{-- ★修正ボタン --}}
            @if (!$hasRequest)
                <div class="btn-area">
                    <button type="submit" class="edit-btn">修正</button>
                </div>
            </form>
            @endif

            {{-- ★承認待ち用メッセージ --}}
            @if ($hasRequest && $correctionRequest->status === 0)
                <p class="error-text right">
                    ※ 承認待ちのため修正はできません。
                </p>
            @endif

        </div>
    </div>
</div>
@endsection
