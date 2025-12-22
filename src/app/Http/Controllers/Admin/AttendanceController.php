<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        // 全社員の当日勤怠
        $attendances = Attendance::with('user')->get();

        return view('admin.attendance.list', compact('attendances'));
    }
}
