<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GownCollection extends Model
{
    protected $fillable = [
        'user_id', 'size', 'collection_date', 'return_date', 'status',
        'gown_session_id', // 👈 add
    ];

    protected $casts = [
        'collection_date' => 'datetime',
        'return_date'     => 'datetime',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function session()  { return $this->belongsTo(GownSession::class, 'gown_session_id'); }
}
