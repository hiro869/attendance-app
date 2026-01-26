<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceCorrectionRequestRequest;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;

class StampCorrectionRequestController extends Controller
{
 public function index(Request $request)
{
    $tab = $request->query('tab', 'pending');

    $query = AttendanceCorrectionRequest::with(['user', 'attendance']);

    if (!auth()->user()->is_admin) {
        $query->where('user_id', auth()->id());
    }

    if ($tab === 'pending') {
        $query->where('status', 0)
              ->orderBy('created_at', 'desc');
    } else {
        $query->where('status', 1)
              ->orderBy('updated_at', 'desc');
    }

    $requests = $query->get();

    return view('request.index', compact('requests', 'tab'));
}

    /**
     * 修正申請保存
     */
public function store(
    AttendanceCorrectionRequestRequest $request,
    $id
) {
    $attendance = Attendance::findOrFail($id);
    $workDate = $attendance->work_date->format('Y-m-d');

    // 休憩配列を datetime に変換
    $requestBreaks = [];

    foreach ($request->breaks ?? [] as $b) {
        if (empty($b['start']) || empty($b['end'])) {
            continue;
        }

        $requestBreaks[] = [
            'start' => "{$workDate} {$b['start']}",
            'end'   => "{$workDate} {$b['end']}",
        ];
    }

    AttendanceCorrectionRequest::create([
        'attendance_id'      => $attendance->id,
        'user_id'            => auth()->id(),

        'request_start_time' => $request->start_time
            ? "{$workDate} {$request->start_time}"
            : null,

        'request_end_time'   => $request->end_time
            ? "{$workDate} {$request->end_time}"
            : null,

        // ★ breaks 配列を保存
        'request_breaks'     => $requestBreaks ?: null,

        'note'               => $request->note,
        'status'             => 0,
    ]);

    return redirect()
        ->route('attendance.detail.byDate', $attendance->work_date)
        ->with('success', '修正申請を送信しました');
}
}
