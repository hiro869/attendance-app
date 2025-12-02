<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
});
// 勤怠一覧
Route::get('/attendance/list', [AttendanceController::class, 'list'])
    ->name('attendance.list');

// 今月の出勤一覧
Route::get('/attendance/month', [AttendanceController::class, 'month'])
    ->name('attendance.month');

// 申請一覧（あなたの環境に合わせる）
Route::get('/application', [ApplicationController::class, 'index'])
    ->name('application.index');



