<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            // 未入力
            'email.required'    => 'メールアドレスを入力してください',
            'email.email'       => 'メールアドレスを入力してください', // 要件では形式違いの文言指定なし
            'password.required' => 'パスワードを入力してください',
        ];
    }
}
