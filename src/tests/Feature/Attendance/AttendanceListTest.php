<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_only_his_own_attendance_list()
    {
        Carbon::setTestNow('2026-01-15');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 自分の勤怠（2日分）
        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-01-10',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2026-01-11',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ]);

        // 他人の勤怠（表示されてはいけない）
        Attendance::factory()->create([
            'user_id' => $otherUser->id,
            'work_date' => '2026-01-10',
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.list'));

        $response->assertStatus(200);

        // 自分の勤怠は見える
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('17:00');

        // 他人の勤怠は見えない
        $response->assertDontSee('10:00');
        $response->assertDontSee('19:00');
    }
    /** @test */
public function current_month_is_displayed_when_opening_attendance_list()
{
    // 今を固定
    Carbon::setTestNow('2026-01-15');

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('attendance.list'));

    $response->assertStatus(200);

    // Bladeで表示されている想定の形式に合わせる
    // 例：2026年1月
    $response->assertSee('2026/01');
}
/** @test */
public function previous_month_is_displayed_when_clicking_prev()
{
    Carbon::setTestNow(Carbon::create(2026, 1, 15));

    $user = User::factory()->create();

    // 前月（2025-12）
    Attendance::factory()->create([
        'user_id'   => $user->id,
        'work_date' => '2025-12-10',
        'start_time'=> '09:00',
        'end_time'  => '18:00',
    ]);

    $response = $this->actingAs($user)
        ->get(route('attendance.list', ['month' => '2025-12']));

    $response->assertStatus(200);

    // Blade表示形式に合わせる
    $response->assertSee('2025/12');
}
/** @test */
public function next_month_is_displayed_when_clicking_next()
{
    Carbon::setTestNow(Carbon::create(2026, 1, 15));

    $user = User::factory()->create();

    Attendance::factory()->create([
        'user_id'   => $user->id,
        'work_date' => '2026-02-05',
        'start_time'=> '09:00',
        'end_time'  => '18:00',
    ]);

    $response = $this->actingAs($user)
        ->get(route('attendance.list', ['month' => '2026-02']));

    $response->assertStatus(200);

    $response->assertSee('2026/02');
}
/** @test */
public function user_can_navigate_to_attendance_detail_page()
{
    Carbon::setTestNow(Carbon::create(2026, 1, 10));

    $user = User::factory()->create();

    Attendance::factory()->create([
        'user_id'   => $user->id,
        'work_date' => '2026-01-10',
        'start_time'=> '09:00',
        'end_time'  => '18:00',
    ]);

    $response = $this->actingAs($user)
        ->get(route('attendance.detail.byDate', ['date' => '2026-01-10']));

    $response->assertStatus(200);

    // 詳細画面の固定文言（どれか1つ）
    $response->assertSee('勤怠詳細');
}


}
