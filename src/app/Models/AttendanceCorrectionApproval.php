<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'correction_request_id',
        'admin_id',
        'approved_at',
    ];

    public function correctionRequest()
    {
        return $this->belongsTo(AttendanceCorrectionRequest::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

