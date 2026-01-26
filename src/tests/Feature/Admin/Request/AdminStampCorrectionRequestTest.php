<?php

namespace Tests\Feature\Admin\Request;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Carbon\Carbon;

class AdminStampCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 管理者は承認待ちの修正申請を確認できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create(['role' => 'user']);

        AttendanceCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'status'  => 0, // 承認待ち
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.request.list'));

        $response->assertStatus(200);
        $response->assertSee('承認待ち');
    }

    #[Test]
    public function 管理者は承認済みの修正申請を確認できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create(['role' => 'user']);

        AttendanceCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'status'  => 1, // 承認済み
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.request.list'));

        $response->assertStatus(200);
        $response->assertSee('承認済み');
    }

    #[Test]
    public function 管理者は修正申請の詳細内容を確認できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create(['role' => 'user']);

        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'work_date' => Carbon::now()->toDateString(),
        ]);

        $request = AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id'       => $user->id,
            'note'          => '修正理由テスト',
            'status'        => 0,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.request.approve', $request->id));

        $response->assertStatus(200);
        $response->assertSee('修正理由テスト');
        $response->assertSee($user->name);
    }

    #[Test]
    public function 管理者は修正申請を承認できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create(['role' => 'user']);

        $attendance = Attendance::factory()->create([
            'user_id'    => $user->id,
            'start_time' => '09:00',
            'end_time'   => '18:00',
        ]);

        $request = AttendanceCorrectionRequest::factory()->create([
            'attendance_id'      => $attendance->id,
            'user_id'            => $user->id,
            'request_start_time' => '10:00',
            'request_end_time'   => '19:00',
            'note'               => '承認テスト',
            'status'             => 0,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.request.approve', $request->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('correction_requests', [
            'id'     => $request->id,
            'status' => 1,
        ]);
    }
}
