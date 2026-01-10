<?php

namespace App\Http\Controllers;

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
}}
