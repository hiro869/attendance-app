<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class StampCorrectionRequestController extends Controller
{
    /**
     * 申請一覧（承認待ち / 承認済み）
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'pending');

        $query = AttendanceCorrectionRequest::with(['user', 'attendance'])
            ->orderBy(
                $tab === 'pending' ? 'created_at' : 'updated_at',
                'desc'
            );

        if ($tab === 'pending') {
            $query->where('status', 0);
        } else {
            $query->where('status', 1);
        }

        $requests = $query->get();

        return view('admin.request.list', compact('requests', 'tab'));
    }

    /**
     * 申請詳細 / 承認
     */

public function approve(Request $request, $id)
{
    $correction = AttendanceCorrectionRequest::with(['attendance', 'user'])
        ->findOrFail($id);

    // GET → 詳細表示
    if ($request->isMethod('get')) {
        return view('admin.request.approve', compact('correction'));
    }

    // POST → 承認処理
    DB::transaction(function () use ($correction) {

        $attendance = $correction->attendance;

        // ① 勤怠更新
        $attendance->update([
            'start_time' => $correction->request_start_time,
            'end_time'   => $correction->request_end_time,
            'note'       => $correction->note,
        ]);

        // ② 休憩を更新
        $attendance->breaks()->delete();

        if (!empty($correction->request_breaks)) {
            foreach ($correction->request_breaks as $b) {
                $attendance->breaks()->create([
                    'break_start' => $b['start'],
                    'break_end'   => $b['end'],
                ]);
            }
        }

        // ③ 申請を承認済みに
        $correction->update([
            'status' => 1,
        ]);
    });

    return redirect()
        ->route('admin.request.list', ['tab' => 'approved'])
        ->with('success', '修正申請を承認しました');
}
}
