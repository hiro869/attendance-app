<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceCorrectionRequestRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\AttendanceCorrectionRequest;


class AttendanceController extends Controller
{
    /**
     * 打刻画面
     */

public function index(Request $request)
{
    if ($request->is('attendance')) {

        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', Carbon::today())
            ->first();

        return view('attendance.index', compact('attendance'));
    }
    $user = auth()->user();

    $current = $request->month
        ? Carbon::parse($request->month . '-01')
        : Carbon::now()->startOfMonth();

    $prev = $current->copy()->subMonth()->format('Y-m');
    $next = $current->copy()->addMonth()->format('Y-m');

    $period = CarbonPeriod::create(
        $current->copy()->startOfMonth(),
        $current->copy()->endOfMonth()
    );

    $attendances = Attendance::with('breaks')
        ->where('user_id', $user->id)
        ->whereBetween('work_date', [
            $current->copy()->startOfMonth(),
            $current->copy()->endOfMonth()
        ])
        ->get()
        ->keyBy(fn ($att) => $att->work_date->toDateString());

    $week = ['日','月','火','水','木','金','土'];
    $rows = [];

    foreach ($period as $date) {
        $key = $date->toDateString();
        $att = $attendances[$key] ?? null;

        $start = $att?->start_time?->format('H:i') ?? ' ';
        $end   = $att?->end_time?->format('H:i') ?? ' ';

        $breakSec = 0;
        if ($att) {
            foreach ($att->breaks as $b) {
                if ($b->break_start && $b->break_end) {
                    $breakSec += $b->break_start->diffInSeconds($b->break_end);
                }
            }
        }

        $break = $breakSec
            ? sprintf('%d:%02d', intdiv($breakSec,3600), intdiv($breakSec%3600,60))
            : ' ';

        if ($att && $att->start_time && $att->end_time) {
            $workSec = max(
                $att->start_time->diffInSeconds($att->end_time) - $breakSec,
                0
            );
            $total = sprintf('%d:%02d', intdiv($workSec,3600), intdiv($workSec%3600,60));
        } else {
            $total = ' ';
        }

        $rows[] = [
            'id'    => $att?->id,
            'raw_date' => $date->toDateString(),
            'date'  => $date->format('m/d') . '(' . $week[$date->dayOfWeek] . ')',
            'start' => $start,
            'end'   => $end,
            'break' => $break,
            'total' => $total,
        ];
    }

    return view('attendance.list', compact(
        'rows','current','prev','next'
    ));
}
/**
 * 打刻処理
 */
public function store(Request $request)
{
    $user = Auth::user();
    $today = Carbon::today()->toDateString();
    $action = $request->input('action');

    // 今日の勤怠（1日1件）
    $attendance = Attendance::firstOrCreate(
        [
            'user_id'   => $user->id,
            'work_date' => $today,
        ],
        [
            'status' => 0, // 勤務外
        ]
    );

    // ===============================
    // 出勤
    // ===============================
    if ($action === 'start') {
        if ($attendance->start_time) return back();

        $attendance->update([
            'start_time' => now(),
            'status'     => 1, // 出勤中
        ]);
        return back();
    }

    // ===============================
    // 休憩開始
    // ===============================
    if ($action === 'break_start') {
        if ($attendance->status !== 1) return back();

        // 未終了の休憩がなければ作成
        if (!$attendance->breaks()->whereNull('break_end')->exists()) {
            $attendance->breaks()->create([
                'break_start' => now(),
            ]);
            $attendance->update(['status' => 2]); // 休憩中
        }
        return back();
    }

    // ===============================
    // 休憩終了
    // ===============================
    if ($action === 'break_end') {
        if ($attendance->status !== 2) return back();

        $break = $attendance->breaks()->whereNull('break_end')->first();
        if ($break) {
            $break->update([
                'break_end' => now(),
            ]);
        }

        $attendance->update(['status' => 1]); // 出勤中
        return back();
    }

    // ===============================
    // 退勤
    // ===============================
    if ($action === 'end') {
        if (!$attendance->start_time || $attendance->end_time) return back();

        $attendance->update([
            'end_time' => now(),
            'status'   => 3, // 退勤済
        ]);

        return back()->with('message', 'お疲れ様でした。');
    }

    return back();
}


public function detail($id)
{
    $attendance = Attendance::with('breaks')->findOrFail($id);

    // ★ 修正申請（承認待ち）を取得
    $correctionRequest = AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
        ->where('status', 0) // 承認待ちのみ
        ->latest()
        ->first();

    return view('attendance.detail', [
        'attendance'        => $attendance,
        'breaks'            => $attendance->breaks,
        'correctionRequest' => $correctionRequest, // ★ 必ず渡す
    ]);
}
    public function detailByDate($date)
{
    $user = auth()->user();
    $date = Carbon::parse($date)->toDateString();

    $attendance = Attendance::with('breaks')
        ->where('user_id', $user->id)
        ->where('work_date', $date)
        ->first(); // ← null OK

    $correctionRequest = $attendance
        ? AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('status', 0)
            ->latest()
            ->first()
        : null;

    return view('attendance.detail', [
        'attendance'        => $attendance,
        'breaks'            => $attendance?->breaks ?? collect(),
        'correctionRequest' => $correctionRequest,
        'date'              => Carbon::parse($date),
    ]);
}
}
