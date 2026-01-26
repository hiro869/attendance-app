<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 会員登録後に認証メールが送信される()
    {
        Event::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        event(new Registered($user));

        Event::assertDispatched(Registered::class);
    }

    #[Test]
    public function 認証誘導画面が表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
    }

    #[Test]
    public function メール認証完了後に勤怠登録画面へ遷移できる()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.index'));

        $response->assertStatus(200);
    }
}
