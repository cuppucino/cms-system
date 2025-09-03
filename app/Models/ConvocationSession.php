<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConvocationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',      // e.g. 'Morning', 'Afternoon'
        'date',
        'location',
        'quota',
        'guest_quota',
        'guest_registered',
    ];

    protected $casts = ['date' => 'date', 'time' => 'datetime:H:i'];

    /**
     * Relationship: Registrations for this session
     */
    public function registrations()
    {
        return $this->hasMany(SessionRegistration::class);
    }

    /**
     * Relationship: Invitations for this session
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'convocation_session_id');
    }

    /**
     * Relationship: Attendance records for this session
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }
}
