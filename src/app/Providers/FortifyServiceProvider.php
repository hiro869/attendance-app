<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse; // ★ 追加
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\AdminLoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*--------------------------------------------
        |  会員登録
        ---------------------------------------------*/
        Fortify::createUsersUsing(CreateNewUser::class);

        // 登録後の遷移
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect('/attendance');
            }
        });

        
        /*--------------------------------------------
        | ★ ログアウト後の遷移（これが今回の追加）
        ---------------------------------------------*/
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                return redirect('/login');  // ← ログアウト後にログイン画面へ
            }
        });

        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.attendance.list');
            }
                return redirect()->route('attendance.index');
            }
        });

        /*--------------------------------------------
        | ログイン画面（一般 / 管理者切り替え）
        ---------------------------------------------*/
        Fortify::loginView(function () {

        // 管理者側URLなら管理者ログインビューを返す
        if (request()->is('admin/*')) {
            return view('admin.auth.login');
        }

        // それ以外は一般ログインビュー
        return view('auth.login');
        });

        /*--------------------------------------------
        | 会員登録画面
        ---------------------------------------------*/
        Fortify::registerView(function () {
            return view('auth.register');
        });


        Fortify::authenticateUsing(function (Request $request) {

    // =========================
    // ① FormRequest を切り替える
    // =========================
    if ($request->is('admin/*')) {
        $formRequest = new AdminLoginRequest();
    } else {
        $formRequest = new LoginRequest();
    }

    $formRequest->setContainer(app())
                ->setRedirector(app('redirect'));

    $formRequest->merge($request->all());
    $formRequest->validateResolved();

    $validated = $formRequest->validated();

    // =========================
    // ② ユーザー取得
    // =========================
    $user = \App\Models\User::where('email', $validated['email'])->first();

    // =========================
    // ③ 認証失敗（要件 FN016）
    // =========================
    if (
        !$user ||
        !Hash::check($validated['password'], $user->password)
    ) {
        throw ValidationException::withMessages([
            'email' => ['ログイン情報が登録されていません'],
        ]);
    }

    // =========================
    // ④ 管理者チェック（超重要）
    // =========================
    if ($request->is('admin/*') && $user->role !== 'admin') {
        throw ValidationException::withMessages([
            'email' => ['ログイン情報が登録されていません'],
        ]);
    }

    return $user;
});


        /*--------------------------------------------
        | レートリミット
        ---------------------------------------------*/
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email . $request->ip());
        });
    }
}
