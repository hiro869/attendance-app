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

        <div class="detail-card">

            @php
                $hasAttendance = !is_null($attendance);
                $hasRequest = $hasAttendance && !is_null($correctionRequest);
            @endphp

            {{-- 修正可能な場合のみ form --}}
            @if ($hasAttendance && !$hasRequest)
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
                    <td>
                        @if ($attendance)
                            {{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年m月d日') }}
                        @else
                            {{ \Carbon\Carbon::parse($date)->format('Y年m月d日') }}
                        @endif
                    </td>
                </tr>

                {{-- 出勤・退勤 --}}
                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-row">
                        @if ($hasAttendance && !$hasRequest)
                            <input type="time" name="start_time"
                                value="{{ old('start_time', $attendance->start_time?->format('H:i')) }}">
                            〜
                            <input type="time" name="end_time"
                                value="{{ old('end_time', $attendance->end_time?->format('H:i')) }}">

                            @error('start_time')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                            @error('end_time')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        @else
                            {{ $attendance?->start_time?->format('H:i') ?? 'ー' }}
                            〜
                            {{ $attendance?->end_time?->format('H:i') ?? 'ー' }}
                        @endif
                    </td>
                </tr>

                {{-- =========================
                     休憩（回数分）
                ========================= --}}
                @foreach ($breaks as $i => $break)
                <tr>
                    <th>休憩{{ $i + 1 }}</th>
                    <td class="time-row">
                        @if ($hasAttendance && !$hasRequest)
                            <input type="time" name="breaks[{{ $i }}][start]"
                                value="{{ old("breaks.$i.start", $break->break_start?->format('H:i')) }}">
                            〜
                            <input type="time" name="breaks[{{ $i }}][end]"
                                value="{{ old("breaks.$i.end", $break->break_end?->format('H:i')) }}">

                            @error("breaks.$i.start")
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                            @error("breaks.$i.end")
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        @else
                            {{ $break->break_start?->format('H:i') ?? 'ー' }}
                            〜
                            {{ $break->break_end?->format('H:i') ?? 'ー' }}
                        @endif
                    </td>
                </tr>
                @endforeach

                {{-- =========================
                     休憩（追加用1件）
                ========================= --}}
                @if ($hasAttendance && !$hasRequest)
                <tr>
                    <th>休憩{{ $breaks->count() + 1 }}</th>
                    <td class="time-row">
                        <input type="time" name="breaks[new][start]">
                        〜
                        <input type="time" name="breaks[new][end]">

                        @error('breaks.new.start')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                        @error('breaks.new.end')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                @endif

                {{-- 備考 --}}
                <tr>
                    <th>備考</th>
                    <td>
                        @if ($hasAttendance && !$hasRequest)
                            <input type="text"
                                name="note"
                                value="{{ old('note') }}"
                                placeholder="修正理由を入力してください">

                            @error('note')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        @else
                            {{ $hasRequest ? $correctionRequest->note : ($attendance?->note ?? 'ー') }}
                        @endif
                    </td>
                </tr>

            </table>

            {{-- 修正ボタン --}}
            @if ($hasAttendance && !$hasRequest)
                <div class="btn-area">
                    <button type="submit" class="edit-btn">修正</button>
                </div>
                </form>
            @endif

            {{-- メッセージ --}}
            @if (!$hasAttendance)
                <p class="error-text right">
                    ※ この日は勤怠データが存在しないため、修正はできません。
                </p>
            @elseif ($hasRequest)
                <p class="error-text right">
                    ※ 承認待ちのため修正はできません。
                </p>
            @endif

        </div>
    </div>
</div>
@endsection
