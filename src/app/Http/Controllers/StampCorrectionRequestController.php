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

    $query = AttendanceCorrectionRequest::with(['user', 'attendance'])
        ->where('user_id', auth()->id());

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
public function store(AttendanceCorrectionRequestRequest $request, $id)
{
    $attendance = Attendance::findOrFail($id);

    AttendanceCorrectionRequest::create([
        'attendance_id'      => $attendance->id,
        'user_id'            => auth()->id(),
        'request_start_time' => $request->start_time
            ? $attendance->work_date . ' ' . $request->start_time
            : null,
        'request_end_time'   => $request->end_time
            ? $attendance->work_date . ' ' . $request->end_time
            : null,
        'request_breaks'     => $request->break_start && $request->break_end
            ? [[
                'start' => $request->break_start,
                'end'   => $request->break_end,
            ]]
            : null,
        'note'               => $request->note,
        'status'             => 0, // 承認待ち
    ]);

    return redirect()
        ->route('attendance.detail', $attendance->id)
        ->with('success', '修正申請を送信しました');
}
}
