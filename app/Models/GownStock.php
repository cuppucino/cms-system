<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GownStock extends Model
{
    protected $fillable = ['size', 'total', 'issued', 'available'];

    // Auto-calc available when creating/updating
    protected static function booted()
    {
        static::creating(function ($stock) {
            $stock->issued = $stock->issued ?? 0;
            $stock->available = $stock->total - $stock->issued;
        });

        static::updating(function ($stock) {
            $stock->available = $stock->total - $stock->issued;
        });
    }
}
