<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_start_work_when_off_duty()
    {
        Carbon::setTestNow('2026-01-26 09:00');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('attendance.store'), [
                'action' => 'start',
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status'  => 1, // 出勤中
        ]);
    }

    /** @test */
    public function status_becomes_working_after_start()
    {
        Carbon::setTestNow('2026-01-26 09:00');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('attendance.store'), [
                'action' => 'start',
            ]);

        $attendance = Attendance::first();

        $this->assertEquals(1, $attendance->status);
        $this->assertNotNull($attendance->start_time);
    }

    /** @test */
    public function user_cannot_start_work_twice_in_one_day()
    {
        Carbon::setTestNow('2026-01-26 09:00');

        $user = User::factory()->create();

        // すでに退勤済の勤怠が存在する状態
        Attendance::factory()->create([
            'user_id'    => $user->id,
            'work_date'  => now()->toDateString(),
            'start_time' => now()->subHours(8),
            'end_time'   => now(),
            'status'     => 3, // 退勤済
        ]);

        // もう一度「出勤」
        $this->actingAs($user)
            ->post(route('attendance.store'), [
                'action' => 'start',
            ])
            ->assertStatus(302);

        // 勤怠レコードは1件のまま
        $this->assertDatabaseCount('attendances', 1);
    }
}
