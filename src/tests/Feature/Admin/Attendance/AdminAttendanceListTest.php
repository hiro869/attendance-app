<?php

namespace Tests\Feature\Admin\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

  /** @test */
public function 管理者は当日の全ユーザーの勤怠情報を確認できる()
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $users = User::factory()->count(2)->create([
        'role' => 'user',
    ]);

    foreach ($users as $user) {
        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
        ]);
    }

    $response = $this->actingAs($admin)
        ->get(route('admin.attendance.list'));

    $response->assertStatus(200);

    foreach ($users as $user) {
        $response->assertSee($user->name);
    }
}
/** @test */
public function 管理者勤怠一覧に今日の日付が表示される()
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.attendance.list'));

    $response->assertStatus(200);
    $response->assertSee(now()->format('Y/m/d'));
}
/** @test */
public function 管理者は前日の勤怠一覧を確認できる()
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $yesterday = now()->subDay()->toDateString();

    $response = $this->actingAs($admin)
        ->get(route('admin.attendance.list', [
            'date' => $yesterday,
        ]));

    $response->assertStatus(200);

    // 前日の日付が表示されていること
    $response->assertSee(
        \Carbon\Carbon::parse($yesterday)->format('Y/m/d')
    );
}
/** @test */
public function 管理者は翌日の勤怠一覧を確認できる()
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $tomorrow = now()->addDay()->toDateString();

    $response = $this->actingAs($admin)
        ->get(route('admin.attendance.list', [
            'date' => $tomorrow,
        ]));

    $response->assertStatus(200);

    // 翌日の日付が表示されていること
    $response->assertSee(
        \Carbon\Carbon::parse($tomorrow)->format('Y/m/d')
    );
}

}
