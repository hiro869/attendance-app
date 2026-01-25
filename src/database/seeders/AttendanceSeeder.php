<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        // ダミーデータ（出勤・退勤・休憩の時間）
        $attendance1 = Attendance::create([
            'user_id' => 1,
            'work_date' => '2026-01-01',
            'start_time' => '2026-01-01 09:00:00',
            'end_time' => '2026-01-01 18:00:00',
            'status' => '勤務外', // 状態を追加
        ]);

        // 休憩データ
        BreakTime::create([
            'attendance_id' => $attendance1->id, // 出勤データのIDを関連付け
            'break_start' => '2026-01-01 12:00:00',
            'break_end' => '2026-01-01 12:30:00',
        ]);

        // 一般ユーザー用のダミーデータ
        $attendance2 = Attendance::create([
            'user_id' => 2,
            'work_date' => '2026-01-01',
            'start_time' => '2026-01-01 09:00:00',
            'end_time' => '2026-01-01 18:00:00',
            'status' => '勤務外', // 状態を追加
        ]);

        // 休憩データ
        BreakTime::create([
            'attendance_id' => $attendance2->id, // 出勤データのIDを関連付け
            'break_start' => '2026-01-01 13:00:00',
            'break_end' => '2026-01-01 13:30:00',
        ]);
    }
}
