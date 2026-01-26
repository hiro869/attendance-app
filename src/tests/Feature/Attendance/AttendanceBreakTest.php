<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceBreakTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_start_break_when_working()
    {
        Carbon::setTestNow('2026-01-26 10:00');

        $user = User::factory()->create();

        Attendance::factory()
            ->working()
            ->create([
                'user_id' => $user->id,
                'work_date' => '2026-01-26',
            ]);

        $this->actingAs($user)
            ->post(route('attendance.store'), [
                'action' => 'break_start',
            ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status'  => 2, // 休憩中
        ]);
    }

    /** @test */
    public function user_can_end_break_and_return_to_working()
    {
        Carbon::setTestNow('2026-01-26 10:00');

        $user = User::factory()->create();

        $attendance = Attendance::factory()
            ->onBreak()
            ->create([
                'user_id' => $user->id,
                'work_date' => '2026-01-26',
            ]);

        // 休憩開始レコードを作成
        $attendance->breaks()->create([
            'break_start' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('attendance.store'), [
                'action' => 'break_end',
            ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status'  => 1, // 出勤中に戻る
        ]);
    }

    /** @test */
    public function user_can_take_multiple_breaks_in_one_day()
    {
        Carbon::setTestNow('2026-01-26 10:00');

        $user = User::factory()->create();

        $attendance = Attendance::factory()
            ->working()
            ->create([
                'user_id' => $user->id,
                'work_date' => '2026-01-26',
            ]);

        // 1回目
        $this->actingAs($user)->post(route('attendance.store'), ['action' => 'break_start']);
        $this->actingAs($user)->post(route('attendance.store'), ['action' => 'break_end']);

        // 2回目
        $this->actingAs($user)->post(route('attendance.store'), ['action' => 'break_start']);
        $this->actingAs($user)->post(route('attendance.store'), ['action' => 'break_end']);

        $this->assertEquals(2, $attendance->breaks()->count());
    }
}
