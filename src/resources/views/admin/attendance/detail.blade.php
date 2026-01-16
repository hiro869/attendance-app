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

        <div class="detail-card">

            @php
                // 勤怠があるか
                $hasAttendance = !is_null($attendance);

                // 承認待ち（勤怠がある場合のみ）
                $hasRequest = $hasAttendance
                    ? $attendance->correctionRequests()->where('status', 0)->exists()
                    : false;

                // 休憩は1件目
                $break = $hasAttendance ? $attendance->breaks->first() : null;
            @endphp

          @if ($hasAttendance && !$hasRequest)
   <form method="POST"
      action="{{ url('/admin/attendance/'.$attendance->id) }}">
    @csrf
    @method('PUT')
@endif

            <table class="detail-table">

                {{-- 名前 --}}
                <tr>
                    <th>名前</th>
                    <td>{{ $user->name }}</td>
                </tr>

                {{-- 日付 --}}
                <tr>
                    <th>日付</th>
                    <td>{{ $date->format('Y年m月d日') }}</td>
                </tr>

                {{-- 出勤・退勤 --}}
                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-row">
                        @if ($hasAttendance && !$hasRequest)
                            <input type="time" name="start_time"
                                   value="{{ old('start_time', optional($attendance->start_time)->format('H:i')) }}">
                            〜
                            <input type="time" name="end_time"
                                   value="{{ old('end_time', optional($attendance->end_time)->format('H:i')) }}">

                            @error('start_time') <span class="error-text">{{ $message }}</span> @enderror
                            @error('end_time')   <span class="error-text">{{ $message }}</span> @enderror
                        @else
                            {{ $attendance?->start_time?->format('H:i') ?? 'ー' }}
                            〜
                            {{ $attendance?->end_time?->format('H:i') ?? 'ー' }}
                        @endif
                    </td>
                </tr>

                {{-- 休憩 --}}
                <tr>
                    <th>休憩</th>
                    <td class="time-row">
                        @if ($hasAttendance && !$hasRequest)
                            <input type="time" name="break_start"
                                   value="{{ old('break_start', optional($break)->break_start?->format('H:i')) }}">
                            〜
                            <input type="time" name="break_end"
                                   value="{{ old('break_end', optional($break)->break_end?->format('H:i')) }}">

                            @error('break_start') <span class="error-text">{{ $message }}</span> @enderror
                            @error('break_end')   <span class="error-text">{{ $message }}</span> @enderror
                        @else
                            {{ $break?->break_start?->format('H:i') ?? 'ー' }}
                            〜
                            {{ $break?->break_end?->format('H:i') ?? 'ー' }}
                        @endif
                    </td>
                </tr>

                {{-- 備考 --}}
                <tr>
                    <th>備考</th>
                    <td>
                        @if ($hasAttendance && !$hasRequest)
                            <input type="text"
                                   name="note"
                                   value="{{ old('note', $attendance->note) }}"
                                   placeholder="修正理由を入力してください">

                            @error('note') <span class="error-text">{{ $message }}</span> @enderror
                        @else
                            {{ $attendance?->note ?? 'ー' }}
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

            {{-- メッセージ表示 --}}
            @if (!$hasAttendance)
                <p class="error-text">
                    ※ この日は勤怠データが存在しないため、修正はできません。
                </p>
            @elseif ($hasRequest)
                <p class="error-text">
                    ※ 承認待ちのため修正はできません。
                </p>
            @endif

        </div>
    </div>
</div>
@endsection
