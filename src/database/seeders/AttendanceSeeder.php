<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // すでに存在する全ユーザーに対して勤怠を作成
        $users = User::all();

        foreach ($users as $user) {
            $attendance = Attendance::create([
                'user_id'    => $user->id,
                'work_date'  => '2026-01-01',
                'start_time' => '2026-01-01 09:00:00',
                'end_time'   => '2026-01-01 18:00:00',
                'status'     => '勤務外',
            ]);

            // 休憩データ（1件）
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start'   => '2026-01-01 12:00:00',
                'break_end'     => '2026-01-01 12:30:00',
            ]);
        }
    }
}
