<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Request\AttendanceCorrectionRequestRequest;
// use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
// use App\Http\Controllers\Admin\StaffController;
// use App\Http\Controllers\Admin\MonthAttendanceController;
// use App\Http\Controllers\Admin\StampCorrectionRequestController as AdminStampCorrectionRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ロゴ（"/"）をクリックしたとき → 勤怠画面へ
Route::get('/', function () {
    return redirect()->route('attendance.index');
});

// ----------------------------------------
// 一般ユーザー側（要件表の US007〜）
// ----------------------------------------
Route::middleware('auth')->group(function () {

    // 出勤登録画面（出勤・退勤・休憩）
    // /attendance  GET: 画面表示  POST: 打刻処理
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('/attendance', [AttendanceController::class, 'store'])
        ->name('attendance.store');

    // 勤怠一覧画面
    // /attendance/list  GET
    Route::get('/attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');

// 修正申請を先に書く！！（これは POST）
   Route::post(
    '/attendance/{id}/request-correction',
    [StampCorrectionRequestController::class, 'store']
)->name('attendance.requestCorrection');

// 詳細表示
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])
    ->name('attendance.detail');

    
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])
        ->name('request.index');
});
// 管理者側
    Route::get('/admin/login', function () {
        return view('admin.auth.login');
    })->name('admin.login');
