<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Request\AttendanceCorrectionRequestRequest;
// use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
// use App\Http\Controllers\Admin\StaffController;
// use App\Http\Controllers\Admin\MonthAttendanceController;
// use App\Http\Controllers\Admin\StampCorrectionRequestController as AdminStampCorrectionRequestController;

/* ======================
| 🔹一般ユーザー
====================== */
Route::get('/email/verify', function () {return view('auth.verify-email');})->middleware('auth')->name('verification.notice');
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('/attendance', [AttendanceController::class, 'store'])
        ->name('attendance.store');

    Route::get('/attendance/list', [AttendanceController::class, 'index'])
        ->name('attendance.list');

    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])
        ->name('attendance.detail');

    Route::post('/attendance/{id}/request-correction',
        [StampCorrectionRequestController::class, 'store']
    )->name('attendance.requestCorrection');

    Route::get('/stamp_correction_request/list',
        [StampCorrectionRequestController::class, 'index']
    )->name('request.index');

});




/* ======================
| 🔸管理者ユーザー
====================== */
Route::view('/admin/login', 'admin.auth.login')->name('admin.login');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/attendance/list', [\App\Http\Controllers\Admin\AttendanceController::class,'index'])->name('attendance.list');
    Route::get('/attendance/{id}', [\App\Http\Controllers\Admin\AttendanceController::class,'detail'])->name('attendance.detail');
    Route::post('/attendance/{id}/update', [\App\Http\Controllers\Admin\AttendanceController::class, 'update'])->name('attendance.update');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/detail/{user}/{date}', [\App\Http\Controllers\Admin\AttendanceController::class, 'detailByDate'])->name('attendance.detail.byDate');




    Route::get('/staff/list', [\App\Http\Controllers\Admin\StaffController::class,'index'])->name('staff.list');
    Route::get('/attendance/staff/{id}', [\App\Http\Controllers\Admin\MonthAttendanceController::class,'index'])->name('staff.attendance');
    Route::get('/attendance/staff/{id}/csv', [\App\Http\Controllers\Admin\MonthAttendanceController::class,'csv'])->name('staff.attendance.csv');

    Route::get('/stamp_correction_request/list', [\App\Http\Controllers\Admin\StampCorrectionRequestController::class,'index'])->name('request.list');
    Route::match(['get','post'],'/stamp_correction_request/approve/{attendance_correct_request_id}',
        [\App\Http\Controllers\Admin\StampCorrectionRequestController::class,'approve']
    )->name('request.approve');

});
