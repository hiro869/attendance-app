<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID:1-1
     * 名前が未入力の場合、エラーメッセージが表示される
     */
    public function test_register_fails_when_name_is_empty()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }

    /**
     * ID:1-2
     * メールアドレスが未入力の場合
     */
    public function test_register_fails_when_email_is_empty()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /**
     * ID:1-3
     * パスワードが8文字未満
     */
    public function test_register_fails_when_password_is_too_short()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors([
            'password',
            'password_confirmation',
        ]);

    }

    /**
     * ID:1-4
     * パスワードが一致しない
     */
    public function test_register_fails_when_password_confirmation_does_not_match()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password999',
        ]);

            $response->assertSessionHasErrors([
                'password_confirmation' => 'パスワードと一致しません',
            ]);

    }

    /**
     * ID:1-5
     * パスワード未入力
     */
    public function test_register_fails_when_password_is_empty()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /**
     * ID:1-6
     * 正常に登録できる
     */
    public function test_register_succeeds_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/attendance');
    }
}
