<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    use HasFactory;
    
    protected $table = 'correction_requests';


    protected $fillable = [
        'attendance_id',
        'user_id',
        'request_start_time',
        'request_end_time',
        'request_breaks',
        'note',
        'status',
    ];
// app/Models/AttendanceCorrectionRequest.php
protected $casts = [
    'request_start_time' => 'datetime',
    'request_end_time'   => 'datetime',
    'request_breaks'     => 'array',
];


    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
    {
        return $this->hasMany(AttendanceCorrectionApproval::class);
    }
}
