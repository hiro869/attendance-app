<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CurrentDateTimeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function current_datetime_is_displayed_correctly()
    {
        // =========================
        // テスト用の現在時刻を固定
        // =========================
        Carbon::setTestNow(
            Carbon::create(2026, 1, 26, 10, 30)
        );

        // =========================
        // ユーザー作成 & ログイン
        // =========================
        $user = User::factory()->create();
        $this->actingAs($user);

        // =========================
        // 勤怠打刻画面にアクセス
        // =========================
        $response = $this->get('/attendance');

        // =========================
        // ステータス確認
        // =========================
        $response->assertStatus(200);

        // =========================
        // 日付（Bladeの表示形式に合わせる）
        // =========================
        $response->assertSee('2026年1月26日');

        // =========================
        // 時刻
        // =========================
        $response->assertSee('10:30');
    }
}
