<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'start_time',
        'end_time',
        'note',
        'status',
    ];
    protected $casts = [
    'start_time' => 'datetime',
    'end_time'   => 'datetime',
    'work_date'  => 'date',
    'status' => 'integer',

];


    // ユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 休憩履歴
    public function breaks()
    {
        return $this->hasMany(BreakTime::class, 'attendance_id');
    }
    public function hasPendingRequest()
    {
        return $this->correctionRequests()
            ->where('status', 0) // 0 = 承認待ち
            ->exists();
    }

    // 修正申請
    public function correctionRequests()
    {
        return $this->hasMany(AttendanceCorrectionRequest::class);
    }
}
