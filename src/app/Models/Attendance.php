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
        'status',
    ];

    // ユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 休憩履歴
    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    // 修正申請
    public function correctionRequests()
    {
        return $this->hasMany(AttendanceCorrectionRequest::class);
    }
}
