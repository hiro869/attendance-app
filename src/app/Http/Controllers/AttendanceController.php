<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * 勤怠トップ（出勤/休憩/退勤 ボタン表示）
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // 今日の勤怠取得
        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        return view('attendance.index', [
            'attendance' => $attendance,
            'now' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 打刻処理（出勤 / 休憩入 / 休憩戻 / 退勤）
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $action = $request->input('action'); // "start" "break_start" "break_end" "end"

        // 今日の勤怠取得 or 作成
        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            ['status' => 0]
        );

        /*=====================
            勤務外 → 出勤
        =====================*/
        if ($action === 'start') {
            if ($attendance->start_time) {
                return back()->with('error', '出勤は1日に1回のみです');
            }

            $attendance->update([
                'start_time' => Carbon::now(),
                'status' => 1, // 出勤中
            ]);

            return back()->with('success', '出勤しました！');
        }

        /*=====================
            出勤中 → 休憩入
        =====================*/
        if ($action === 'break_start') {
            if ($attendance->status !== 1) {
                return back()->with('error', '休憩に入れません');
            }

            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => Carbon::now(),
            ]);

            $attendance->update(['status' => 2]); // 休憩中

            return back()->with('success', '休憩に入りました');
        }

        /*=====================
            休憩中 → 休憩戻
        =====================*/
        if ($action === 'break_end') {

            if ($attendance->status !== 2) {
                return back()->with('error', '休憩を終了できません');
            }

            $break = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('break_end')
                ->latest()
                ->first();

            if (!$break) {
                return back()->with('error', '終了できる休憩がありません');
            }

            $break->update(['break_end' => Carbon::now()]);

            $attendance->update(['status' => 1]); // 再び出勤中

            return back()->with('success', '休憩から戻りました');
        }

        /*=====================
            出勤中 → 退勤
        =====================*/
        if ($action === 'end') {
            if ($attendance->end_time) {
                return back()->with('error', '退勤は1日に1回のみです');
            }

            $attendance->update([
                'end_time' => Carbon::now(),
                'status' => 3, // 退勤済み
            ]);

            return back()->with('success', 'お疲れ様でした！');
        }

        return back()->with('error', '不正な操作です');
    }
}
