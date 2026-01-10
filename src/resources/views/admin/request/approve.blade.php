@extends('layouts.admin')

@section('content')
<h2>勤怠詳細</h2>

<table>
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
        <td>
            {{ optional($correction->request_start_time)->format('H:i') }}
            〜
            {{ optional($correction->request_end_time)->format('H:i') }}
        </td>
    </tr>

    <tr>
        <th>休憩</th>
        <td>
            @if(!empty($correction->request_breaks))
                @foreach($correction->request_breaks as $b)
                    {{ $b['start'] }} 〜 {{ $b['end'] }}<br>
                @endforeach
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

<form method="POST">
    @csrf
    <button type="submit">承認</button>
</form>
@endsection
