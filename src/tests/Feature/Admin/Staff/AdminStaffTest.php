<?php

namespace Tests\Feature\Admin\Staff;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Carbon\Carbon;

class AdminStaffTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 管理者は全一般ユーザーの氏名とメールアドレスを確認できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $users = User::factory()->count(2)->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.staff.list'));

        $response->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    #[Test]
    public function 管理者は特定ユーザーの勤怠一覧を確認できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create(['role' => 'user']);

        Attendance::factory()->create([
            'user_id'   => $user->id,
            'work_date' => Carbon::now()->startOfMonth()->toDateString(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.staff.attendance', $user->id));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    #[Test]
    public function 管理者は前月の勤怠一覧を確認できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create(['role' => 'user']);

        $prevMonth = Carbon::now()->subMonth()->format('Y-m');

        $response = $this->actingAs($admin)
            ->get(route('admin.staff.attendance', [
                'id'    => $user->id,
                'month' => $prevMonth,
            ]));

        $response->assertStatus(200);
        $response->assertSee($prevMonth);
    }

    #[Test]
    public function 管理者は翌月の勤怠一覧を確認できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create(['role' => 'user']);

        $nextMonth = Carbon::now()->addMonth()->format('Y-m');

        $response = $this->actingAs($admin)
            ->get(route('admin.staff.attendance', [
                'id'    => $user->id,
                'month' => $nextMonth,
            ]));

        $response->assertStatus(200);
        $response->assertSee($nextMonth);
    }

    #[Test]
    public function 管理者は勤怠一覧から詳細画面に遷移できる()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create(['role' => 'user']);

        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'work_date' => Carbon::now()->toDateString(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }
}
