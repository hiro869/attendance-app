<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // 日時は固定（表示影響をなくす）
        Carbon::setTestNow(
            Carbon::create(2026, 1, 26, 10, 30)
        );
    }

    /** @test */
    public function status_is_displayed_as_off_duty()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 勤怠レコードなし = 勤務外
        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    /** @test */
    public function status_is_displayed_as_working()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status'  => 1, // 出勤中
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    /** @test */
    public function status_is_displayed_as_on_break()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status'  => 2, // 休憩中
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    /** @test */
    public function status_is_displayed_as_finished()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status'  => 3, // 退勤済
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }
}
