<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'attendance_token',
        'status',
        'checked_in_at',
    ];
}
