<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceEndTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_end_work_when_working()
    {
        Carbon::setTestNow('2026-01-26 18:00:00');

        $user = User::factory()->create();

        Attendance::factory()
            ->for($user)
            ->working()
            ->create();

        $this->actingAs($user)
            ->post(route('attendance.store'), [
                'action' => 'end',
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status'  => 3,
        ]);
    }
    /** @test */
public function end_time_is_displayed_in_attendance_list()
{
    Carbon::setTestNow('2026-01-26 09:00:00');

    $user = User::factory()->create();

    // 出勤
    $this->actingAs($user)
        ->post(route('attendance.store'), [
            'action' => 'start',
        ]);

    // 時刻を進めて退勤
    Carbon::setTestNow('2026-01-26 18:00:00');

    $this->post(route('attendance.store'), [
        'action' => 'end',
    ]);

    // 勤怠一覧画面を確認
    $response = $this->get(route('attendance.list'));

    $response->assertStatus(200);
    $response->assertSee('18:00');
}

}
