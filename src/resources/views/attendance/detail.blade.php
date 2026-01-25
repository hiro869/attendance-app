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

        @php
            $hasAttendance = !is_null($attendance);
            $editable = $hasAttendance && is_null($correctionRequest);

            if ($editable) {
                $breaksForView = collect($breaks ?? [])->push(null);
            } else {
                $breaksForView = collect($breaks ?? []);
            }
        @endphp

        {{-- 勤怠データなし --}}
        @if (!$hasAttendance)

            <div class="detail-card">
                <table class="detail-table">
                    <tr>
                        <th>名前</th>
                        <td>{{ auth()->user()->name }}</td>
                    </tr>
                    <tr>
                        <th>日付</th>
                        <td>{{ \Carbon\Carbon::parse($date)->format('Y年n月j日') }}</td>
                    </tr>
                    <tr>
                        <th>出勤・退勤</th>
                        <td></td>
                    </tr>
                    <tr>
                        <th>備考</th>
                        <td></td>
                    </tr>
                </table>
            </div>

            <p class="error-text">
                ※ 勤怠データが存在しないため修正できません。
            </p>

        @else

            {{-- 修正可能時のみ form --}}
            @if ($editable)
            <form method="POST" action="{{ route('attendance.requestCorrection', $attendance->id) }}">
                @csrf
            @endif

            <div class="detail-card">

                <table class="detail-table">

                    {{-- 名前 --}}
                    <tr>
                        <th>名前</th>
                        <td>{{ auth()->user()->name }}</td>
                    </tr>

                    {{-- 日付 --}}
                    <tr>
                        <th>日付</th>
                        <td>{{ $attendance->work_date->format('Y年n月j日') }}</td>
                    </tr>

                    {{-- 出勤・退勤 --}}
                    <tr>
                        <th>出勤・退勤</th>
                        <td>
                            @if ($editable)
                                <div class="time-inputs">
                                    <input type="time" name="start_time"
                                        value="{{ old('start_time', optional($attendance->start_time)->format('H:i')) }}">
                                    <span class="wave">〜</span>
                                    <input type="time" name="end_time"
                                        value="{{ old('end_time', optional($attendance->end_time)->format('H:i')) }}">
                                </div>
                                @error('start_time') <span class="error-text">{{ $message }}</span> @enderror
                                @error('end_time') <span class="error-text">{{ $message }}</span> @enderror
                            @else
                                <div class="time-display">
                                    <span>{{ optional($attendance->start_time)->format('H:i') }}</span>
                                    <span class="wave">〜</span>
                                    <span>{{ optional($attendance->end_time)->format('H:i') }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- 休憩 --}}
                    @foreach ($breaksForView as $i => $break)
                    <tr>
                        <th>休憩{{ $i + 1 }}</th>
                        <td>
                            @if ($editable)
                                <div class="time-inputs">
                                    <input type="time" name="breaks[{{ $i }}][start]"
                                        value="{{ old("breaks.$i.start", optional($break?->break_start)->format('H:i')) }}">
                                    <span class="wave">〜</span>
                                    <input type="time" name="breaks[{{ $i }}][end]"
                                        value="{{ old("breaks.$i.end", optional($break?->break_end)->format('H:i')) }}">
                                </div>
                                @error("breaks.$i.start") <span class="error-text">{{ $message }}</span> @enderror
                                @error("breaks.$i.end") <span class="error-text">{{ $message }}</span> @enderror
                            @else
                                <div class="time-display">
                                    <span>{{ optional($break?->break_start)->format('H:i') }}</span>
                                    <span class="wave">〜</span>
                                    <span>{{ optional($break?->break_end)->format('H:i') }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach

                    {{-- 備考 --}}
                    <tr>
                        <th>備考</th>
                        <td>
                            @if ($editable)
                                <textarea name="note" placeholder="修正理由を入力してください">{{ old('note') }}</textarea>
                                @error('note') <span class="error-text">{{ $message }}</span> @enderror
                            @else
                                {{ $attendance->note ?? '' }}
                            @endif
                        </td>
                    </tr>

                </table>

                {{-- 修正ボタン（カード内） --}}
                @if ($editable)
                    <div class="btn-area">
                        <button type="submit" class="edit-btn">修正</button>
                    </div>
                @endif

            </div>

            @if ($editable)
            </form>
            @endif

            {{-- 承認待ち --}}
            @if (!$editable)
                <p class="error-text">
                    ※ 承認待ちのため修正はできません。
                </p>
            @endif

        @endif

    </div>
</div>
@endsection
