<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        return [
            'user_id'   => User::factory(),
            'work_date' => Carbon::today()->toDateString(),
            'start_time'=> null,
            'end_time'  => null,
            'status'    => 0, // 勤務外
        ];
    }

    /**
     * 出勤中
     */
    public function working()
    {
        return $this->state(function () {
            return [
                'start_time' => now()->subHours(1),
                'status'     => 1, // 出勤中
            ];
        });
    }

    /**
     * 休憩中
     */
    public function onBreak()
    {
        return $this->state(function () {
            return [
                'start_time' => now()->subHours(2),
                'status'     => 2, // 休憩中
            ];
        });
    }

    /**
     * 退勤済
     */
    public function finished()
    {
        return $this->state(function () {
            return [
                'start_time' => now()->subHours(8),
                'end_time'   => now(),
                'status'     => 3, // 退勤済
            ];
        });
    }
}
