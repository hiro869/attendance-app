<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class StaffController extends Controller
{
 public function index(Request $request)
    {
        $staffs = User::where('role', 'user')->get();
        return view('admin.staff.list', compact('staffs'));
    }
}

