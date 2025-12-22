<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\AttendanceCorrectionRequest;


class AttendanceController extends Controller
{
    /**
     * 打刻画面
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        return view('attendance.index', compact('attendance'));
    }

    /**
     * 打刻処理
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $action = $request->input('action');

        // ★ 今日の勤怠は必ず1件だけ
        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if (!$attendance) {
            $attendance = Attendance::create([
                'user_id'   => $user->id,
                'work_date'=> $today,
                'status'   => 0,
            ]);
        }

        // 出勤
        if ($action === 'start') {
            if ($attendance->start_time) return back();

            $attendance->update([
                'start_time' => now(),
                'status'     => 1,
            ]);
            return back();
        }

        // 休憩開始
        if ($action === 'break_start') {
            if ($attendance->status !== 1) return back();

            if (!$attendance->breaks()->whereNull('break_end')->exists()) {
                $attendance->breaks()->create([
                    'break_start' => now(),
                ]);
                $attendance->update(['status' => 2]);
            }
            return back();
        }

        // 休憩終了
        if ($action === 'break_end') {
            if ($attendance->status !== 2) return back();

            $break = $attendance->breaks()->whereNull('break_end')->first();
            if ($break) {
                $break->update([
                    'break_end' => now(),
                ]);
            }

            $attendance->update(['status' => 1]);
            return back();
        }

        // 退勤
        if ($action === 'end') {
            if (!$attendance->start_time || $attendance->end_time) return back();

            $attendance->update([
                'end_time' => now(),
                'status'   => 3,
            ]);
            return back();
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

 public function list(Request $request)
{
    $current = $request->month
        ? Carbon::parse($request->month . '-01')
        : Carbon::now()->startOfMonth();

    $prev = $current->copy()->subMonth()->format('Y-m');
    $next = $current->copy()->addMonth()->format('Y-m');
    $week = ['日','月','火','水','木','金','土'];

    $attendances = Attendance::with('breaks')
        ->where('user_id', auth()->id())
        ->whereBetween('work_date', [
            $current->copy()->startOfMonth(),
            $current->copy()->endOfMonth()
        ])
        ->orderBy('work_date')
        ->get()
        ->map(function ($att) use ($week) {

            $w = Carbon::parse($att->work_date);
            $date = $w->format('m/d') . '(' . $week[$w->dayOfWeek] . ')';

            $start = $att->start_time?->format('H:i') ?? 'ー';
            $end   = $att->end_time?->format('H:i') ?? 'ー';

            // ===============================
            // 休憩時間（秒）
            // ===============================
            $breakSec = 0;
            foreach ($att->breaks as $b) {
                if ($b->break_start && $b->break_end) {
                    $breakSec += $b->break_start->diffInSeconds($b->break_end);
                }
            }

            $breakHour = intdiv($breakSec, 3600);
            $breakMin  = intdiv($breakSec % 3600, 60);
            $break = sprintf('%d:%02d', $breakHour, $breakMin);

            // ===============================
            // 勤務時間（秒）
            // ===============================
            if ($att->start_time && $att->end_time) {
                $workSec = $att->start_time->diffInSeconds($att->end_time) - $breakSec;
                $workSec = max($workSec, 0);

                $workHour = intdiv($workSec, 3600);
                $workMin  = intdiv($workSec % 3600, 60);
                $total = sprintf('%d:%02d', $workHour, $workMin);
            } else {
                $total = 'ー';
            }

            return [
                'id'    => $att->id,
                'date'  => $date,
                'start' => $start,
                'end'   => $end,
                'break' => $break,
                'total' => $total,
            ];
        });

    return view('attendance.list', compact('attendances', 'current', 'prev', 'next'));
}
}
