<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use App\Models\AttendanceCorrectionRequest;

class AttendanceDetailUpdateValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function start_time_after_end_time_shows_error()
    {
        Carbon::setTestNow('2026-01-26 10:00:00');

        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'work_date' => '2026-01-26',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('attendance.requestCorrection', $attendance->id), [
                'start_time' => '19:00',
                'end_time'   => '18:00',
                'note'       => 'テスト備考',
            ]);

        $response->assertSessionHasErrors([
            'end_time' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function break_start_after_end_time_shows_error()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user)
            ->post(route('attendance.requestCorrection', $attendance->id), [
                'start_time' => '09:00',
                'end_time'   => '18:00',
                'note'       => 'テスト備考',
                'breaks' => [
                    ['start' => '18:30', 'end' => '18:45'],
                ],
            ]);

        $response->assertSessionHasErrors([
            'breaks.0.start' => '休憩時間が不適切な値です',
        ]);
    }

    /** @test */
    public function break_end_after_end_time_shows_error()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user)
            ->post(route('attendance.requestCorrection', $attendance->id), [
                'start_time' => '09:00',
                'end_time'   => '18:00',
                'note'       => 'テスト備考',
                'breaks' => [
                    ['start' => '17:00', 'end' => '18:30'],
                ],
            ]);

        $response->assertSessionHasErrors([
            'breaks.0.end' => '休憩時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function note_is_required()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user)
            ->post(route('attendance.requestCorrection', $attendance->id), [
                'start_time' => '09:00',
                'end_time'   => '18:00',
            ]);

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください',
        ]);
    }
    /** @test */
public function correction_request_is_created_when_user_updates_attendance()
{
    $user = User::factory()->create();
    $attendance = Attendance::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->post(route('attendance.requestCorrection', $attendance->id), [
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'note'       => '修正申請テスト',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('correction_requests', [
        'attendance_id' => $attendance->id,
        'user_id'       => $user->id,
        'status'        => 0, // 承認待ち
        'note'          => '修正申請テスト',
    ]);
}
/** @test */
public function pending_requests_are_displayed_for_logged_in_user()
{
    $user = User::factory()->create();
    $attendance = Attendance::factory()->create(['user_id' => $user->id]);

    AttendanceCorrectionRequest::factory()->create([
        'attendance_id' => $attendance->id,
        'user_id'       => $user->id,
        'status'        => 0,
        'note'          => '承認待ち申請',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('request.index', ['tab' => 'pending']));

    $response->assertStatus(200);
    $response->assertSee('承認待ち申請');
}
/** @test */
public function approved_requests_are_displayed_in_approved_tab()
{
    $user  = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);

    $attendance = Attendance::factory()->create(['user_id' => $user->id]);

    AttendanceCorrectionRequest::factory()->create([
        'attendance_id' => $attendance->id,
        'user_id'       => $user->id,
        'status'        => 1, // 承認済み
        'note'          => '承認済み申請',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('request.index', ['tab' => 'approved']));

    $response->assertStatus(200);
    $response->assertSee('承認済み申請');
}
/** @test */
public function clicking_detail_navigates_to_attendance_detail_page()
{
    $user = User::factory()->create();
    $attendance = Attendance::factory()->create(['user_id' => $user->id]);

    $request = AttendanceCorrectionRequest::factory()->create([
        'attendance_id' => $attendance->id,
        'user_id'       => $user->id,
        'status'        => 0,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('attendance.detail.byDate', [
            'date' => $attendance->work_date->toDateString()
        ]));

    $response->assertStatus(200);
    $response->assertSee('勤怠詳細');
}

}
