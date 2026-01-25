@extends('layouts.admin')

@push('page_css')
<link rel="stylesheet" href="{{ asset('css/admin/admin-detail.css') }}">
@endpush

@section('content')
<div class="attendance-detail-page">
    <div class="attendance-detail-wrapper">

        {{-- タイトル --}}
        <div class="attendance-detail-title">
            <span class="title-bar"></span>
            <h2>勤怠詳細</h2>
        </div>

        <div class="attendance-detail-card">

            @php
                $hasAttendance = !is_null($attendance);
                $hasRequest = $hasAttendance
                    ? $attendance->correctionRequests()->where('status', 0)->exists()
                    : false;
                $breaks = $hasAttendance ? $attendance->breaks : collect();
            @endphp

            {{-- 修正可能な場合のみ form --}}
            @if ($hasAttendance && !$hasRequest)
                <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}">
                    @csrf
                    @method('PUT')
            @endif

            <table class="attendance-detail-table">
                {{-- 名前 --}}
                <tr>
                    <th>名前</th>
                    <td>{{ $user->name }}</td>
                </tr>

                {{-- 日付 --}}
                <tr>
                    <th>日付</th>
                    <td>{{ $date->format('Y年n月j日') }}</td>
                </tr>

                {{-- 出勤・退勤 --}}
                <tr>
                    <th>出勤・退勤</th>
                    <td class="attendance-time-row">
                        @if ($hasAttendance && !$hasRequest)
                            <input type="time" name="start_time"
                                   value="{{ old('start_time', $attendance->start_time?->format('H:i')) }}">
                            〜
                            <input type="time" name="end_time"
                                   value="{{ old('end_time', $attendance->end_time?->format('H:i')) }}">

                            @error('start_time') <span class="error-text">{{ $message }}</span> @enderror
                            @error('end_time')   <span class="error-text">{{ $message }}</span> @enderror
                        @else
                            @if ($attendance?->start_time && $attendance?->end_time)
                                {{ $attendance->start_time->format('H:i') }}
                                〜
                                {{ $attendance->end_time->format('H:i') }}
                            @endif
                        @endif
                    </td>
                </tr>

                {{-- 休憩（回数分） --}}
                @foreach ($breaks as $i => $break)
                    <tr>
                        <th>休憩{{ $i + 1 }}</th>
                        <td class="attendance-time-row">
                            @if ($hasAttendance && !$hasRequest)
                                <input type="time" name="breaks[{{ $i }}][start]"
                                       value="{{ old("breaks.$i.start", $break->break_start?->format('H:i')) }}">
                                〜
                                <input type="time" name="breaks[{{ $i }}][end]"
                                       value="{{ old("breaks.$i.end", $break->break_end?->format('H:i')) }}">

                                @error("breaks.$i.start") <span class="error-text">{{ $message }}</span> @enderror
                                @error("breaks.$i.end")   <span class="error-text">{{ $message }}</span> @enderror
                            @else
                                @if ($break->break_start && $break->break_end)
                                    {{ $break->break_start->format('H:i') }}
                                    〜
                                    {{ $break->break_end->format('H:i') }}
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach

                {{-- 休憩（追加用1件） --}}
                @if ($hasAttendance && !$hasRequest)
                    <tr>
                        <th>休憩{{ $breaks->count() + 1 }}</th>
                        <td class="attendance-time-row">
                            <input type="time" name="breaks[{{ $breaks->count() }}][start]"
                                   value="{{ old('breaks.' . $breaks->count() . '.start') }}">
                            〜
                            <input type="time" name="breaks[{{ $breaks->count() }}][end]"
                                   value="{{ old('breaks.' . $breaks->count() . '.end') }}">
                            @error("breaks." . $breaks->count() . ".start")
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                            @error("breaks." . $breaks->count() . ".end")
                                <span class="error-text">{{ $message }}</span>
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
                                   value="{{ old('note', $attendance->note) }}"
                                   placeholder="修正理由を入力してください">
                            @error('note') <span class="error-text">{{ $message }}</span> @enderror
                        @else
                            {{ $attendance?->note ?? '' }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- 修正ボタン --}}
        @if ($hasAttendance && !$hasRequest)
            <div class="attendance-btn-area">
                <button type="submit" class="attendance-edit-btn">修正</button>
            </div>
            </form>
        @endif

        {{-- メッセージ --}}
        @if (!$hasAttendance)
            <p class="error-text right-message">
                ※ この日は勤怠データが存在しないため、修正はできません。
            </p>
        @elseif ($hasRequest)
            <p class="error-text right-message">
                ※ 承認待ちのため修正はできません。
            </p>
        @endif

    </div>
</div>
@endsection
