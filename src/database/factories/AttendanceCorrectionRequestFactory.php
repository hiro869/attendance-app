<?php

namespace Database\Factories;

use App\Models\AttendanceCorrectionRequest;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceCorrectionRequestFactory extends Factory
{
    protected $model = AttendanceCorrectionRequest::class;

    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'attendance_id' => Attendance::factory(),
            'status'        => 0, // 承認待ち
            'note'          => 'テスト備考',
        ];
    }
}
