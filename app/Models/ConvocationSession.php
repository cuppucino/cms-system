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
    ];

    // Relationship with students
    public function students()
    {
        return $this->hasMany(User::class);
    }

    public function users()
{
    return $this->hasMany(User::class);
}
public function registrations()
{
    return $this->hasMany(SessionRegistration::class);
}
}
