<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\Admin\AttendanceUpdateRequest;

class AttendanceController extends Controller
{
    /**
     * 勤怠一覧（管理者）
     * ※ 1日1ユーザー=1件が前提。unique() は不要
     */
    public function index(Request $request)
    {
        $date = $request->date ?? now()->toDateString();
        $week = ['日','月','火','水','木','金','土'];

        $w = Carbon::parse($date);
        $dateLabel = $w->format('Y年n月j日').'（'.$week[$w->dayOfWeek].'）の勤怠';

        $attendances = Attendance::with(['user','breaks'])
            ->whereDate('work_date', $date)
            ->orderBy('user_id')
            ->get()
            ->map(function ($att) {

                $start = $att->start_time?->format('H:i') ?? 'ー';
                $end   = $att->end_time?->format('H:i') ?? 'ー';

                // 休憩合計
                $breakSec = 0;
                foreach ($att->breaks as $b) {
                    if ($b->break_start && $b->break_end) {
                        $breakSec += $b->break_start->diffInSeconds($b->break_end);
                    }
                }

                $break = $breakSec
                    ? sprintf('%d:%02d', intdiv($breakSec,3600), intdiv($breakSec%3600,60))
                    : 'ー';

                // 労働時間
                if ($att->start_time && $att->end_time) {
                    $workSec = max(
                        $att->start_time->diffInSeconds($att->end_time) - $breakSec,
                        0
                    );
                    $total = sprintf('%d:%02d', intdiv($workSec,3600), intdiv($workSec%3600,60));
                } else {
                    $total = 'ー';
                }

                return (object)[
                    'id'    => $att->id,
                    'name'  => $att->user->name,
                    'start' => $start,
                    'end'   => $end,
                    'break' => $break,
                    'total' => $total,
                ];
            });

        return view('admin.attendance.list', [
            'attendances' => $attendances,
            'date'        => $date,
            'dateLabel'   => $dateLabel,
            'prevDate'    => Carbon::parse($date)->subDay()->toDateString(),
            'nextDate'    => Carbon::parse($date)->addDay()->toDateString(),
        ]);
    }

    /**
     * 勤怠詳細（ID指定）
     */
    public function detail($id)
    {
        $attendance = Attendance::with(['user','breaks','correctionRequests'])
            ->findOrFail($id);

        return view('admin.attendance.detail', [
            'attendance' => $attendance,
            'user'       => $attendance->user,
            'date'       => Carbon::parse($attendance->work_date),
        ]);
    }

    /**
     * 勤怠修正（管理者：update のみ）
     */
    public function update(AttendanceUpdateRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        // 勤怠更新
        $attendance->update([
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'note'       => $request->note,
        ]);

        // 休憩は作り直す（評価的に安全）
        $attendance->breaks()->delete();

        if ($request->break_start && $request->break_end) {
            $attendance->breaks()->create([
                'break_start' => $request->break_start,
                'break_end'   => $request->break_end,
            ]);
        }

        return redirect()
            ->route('admin.attendance.list')
            ->with('success', '勤怠内容を修正しました');
    }

    /**
     * スタッフ月次勤怠
     */
    public function staffMonth(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        $current = $request->month
            ? Carbon::parse($request->month . '-01')
            : Carbon::now()->startOfMonth();

        $prev = $current->copy()->subMonth()->format('Y-m');
        $next = $current->copy()->addMonth()->format('Y-m');

        $attendances = Attendance::with('breaks')
            ->where('user_id', $staff->id)
            ->whereBetween('work_date', [
                $current->copy()->startOfMonth(),
                $current->copy()->endOfMonth()
            ])
            ->orderBy('work_date')
            ->get();

        return view('admin.attendance.staff_month', compact(
            'staff',
            'attendances',
            'current',
            'prev',
            'next'
        ));
    }

    /**
     * 日付指定の詳細（勤怠なし日もOK）
     * ※ 新規作成はしない（表示のみ）
     */
    public function detailByDate(User $user, $date)
    {
        $date = Carbon::parse($date)->toDateString();

        $attendance = Attendance::with(['breaks','correctionRequests'])
            ->where('user_id', $user->id)
            ->where('work_date', $date)
            ->first();

        return view('admin.attendance.detail', [
            'user'       => $user,
            'attendance' => $attendance,
            'date'       => Carbon::parse($date),
        ]);
    }
}
