<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'name',
        'code',
        'faculty',
        'convocation_session_id',
        'hood_color',
    ];

    // Relationship with students
    public function students()
    {
        return $this->hasMany(User::class);
    }

    public function convocationSession()
    {
       return $this->belongsTo(ConvocationSession::class);
    }
}
