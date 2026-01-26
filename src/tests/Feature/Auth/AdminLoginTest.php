<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID:3-1
     * 管理者ログイン：メールアドレス未入力
     */
    public function test_admin_login_fails_when_email_is_empty()
    {
        User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * ID:3-2
     * 管理者ログイン：パスワード未入力
     */
    public function test_admin_login_fails_when_password_is_empty()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * ID:3-3
     * 管理者ログイン：登録内容と一致しない
     */
    public function test_admin_login_fails_with_invalid_credentials()
    {
        User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}
