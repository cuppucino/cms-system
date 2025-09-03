<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GownStock extends Model
{
    protected $fillable = ['size', 'total', 'issued', 'available'];
}
