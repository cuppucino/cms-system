<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'convocation_session_id',
        'code',
        'payload',
        'issued_at',
        'issued_by',
        'revoked_at',
    ];

    protected $casts = [
        'payload'   => 'array',
        'issued_at' => 'datetime',
        'revoked_at'=> 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(\App\Models\ConvocationSession::class, 'convocation_session_id');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->revoked_at === null;
    }
}
