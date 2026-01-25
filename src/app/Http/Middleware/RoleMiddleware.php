<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // ログインユーザーが存在し、指定されたroleと一致するかを確認
        if (Auth::check() && Auth::user()->role == $role) {
            return $next($request);  // ユーザーが適切なroleを持っていればリクエストを次へ
        }

        // 一致しない場合はログイン画面にリダイレクト
        return redirect('/login');
    }
}
