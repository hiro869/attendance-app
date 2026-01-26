<?php

namespace Tests\Feature\Admin\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 管理者は選択した勤怠詳細を正しく確認できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create(['role' => 'user']);

        $attendance = Attendance::factory()->create([
            'user_id'    => $user->id,
            'work_date'  => '2025-01-10',
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'note'       => '通常勤務',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('2025年1月10日');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('通常勤務');
    }

    #[Test]
    public function 出勤時間が退勤時間より後の場合エラーになる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $attendance = Attendance::factory()->create();

        $response = $this->actingAs($admin)
            ->put(route('admin.attendance.update', $attendance->id), [
                'start_time' => '19:00',
                'end_time'   => '18:00',
                'note'       => '修正',
            ]);

        $response->assertSessionHasErrors();
    }

    #[Test]
    public function 休憩開始時間が退勤時間より後の場合エラーになる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $attendance = Attendance::factory()->create(['end_time' => '18:00']);

        $response = $this->actingAs($admin)
            ->put(route('admin.attendance.update', $attendance->id), [
                'start_time' => '09:00',
                'end_time'   => '18:00',
                'breaks' => [
                    ['start' => '19:00', 'end' => '19:30'],
                ],
                'note' => '修正',
            ]);

        $response->assertSessionHasErrors();
    }

    #[Test]
    public function 休憩終了時間が退勤時間より後の場合エラーになる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $attendance = Attendance::factory()->create(['end_time' => '18:00']);

        $response = $this->actingAs($admin)
            ->put(route('admin.attendance.update', $attendance->id), [
                'start_time' => '09:00',
                'end_time'   => '18:00',
                'breaks' => [
                    ['start' => '17:00', 'end' => '19:00'],
                ],
                'note' => '修正',
            ]);

        $response->assertSessionHasErrors();
    }

    #[Test]
    public function 備考が未入力の場合エラーになる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $attendance = Attendance::factory()->create();

        $response = $this->actingAs($admin)
            ->put(route('admin.attendance.update', $attendance->id), [
                'start_time' => '09:00',
                'end_time'   => '18:00',
                'note'       => '',
            ]);

        $response->assertSessionHasErrors('note');
    }
}
