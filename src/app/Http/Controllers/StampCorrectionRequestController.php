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

        $pending = AttendanceCorrectionRequest::where('user_id', auth()->id())
            ->where('status', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        $approved = AttendanceCorrectionRequest::where('user_id', auth()->id())
            ->where('status', 1)
            ->orderBy('updated_at', 'desc')
            ->get();

        $requests = $tab === 'pending' ? $pending : $approved;

        return view('request.index', compact('requests', 'tab'));
    }

    /**
     * 修正申請保存
     */
    public function store(AttendanceCorrectionRequestRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        AttendanceCorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id'       => auth()->id(),
            'start_time'    => $request->start_time,
            'end_time'      => $request->end_time,
            'break_start'   => $request->break_start,
            'break_end'     => $request->break_end,
            'note'          => $request->note,
            'status'        => 0,
        ]);

        return redirect()->route('attendance.detail', $attendance->id);
    }
}
