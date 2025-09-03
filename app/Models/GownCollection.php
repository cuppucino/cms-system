<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GownCollection extends Model
{
    protected $fillable = ['user_id', 'size', 'collection_date', 'return_date', 'status'];

    protected $casts = [
        'collection_date' => 'datetime',
        'return_date'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
