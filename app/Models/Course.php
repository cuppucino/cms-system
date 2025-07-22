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
    ];

    // Relationship with students
    public function students()
    {
        return $this->hasMany(User::class);
    }
}
