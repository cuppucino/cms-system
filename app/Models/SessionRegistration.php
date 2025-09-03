<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'convocation_session_id',
        'guest_count',
        'attendance_confirmed',
        'gown_size',
        'collection_date',
    ];

    protected $casts = [
        'attendance_confirmed' => 'boolean',
        'collection_date'      => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(ConvocationSession::class, 'convocation_session_id');
    }
}
