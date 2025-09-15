<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GownSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'location',
        'quota',
        'note',
    ];

    protected $casts = [
        'date'       => 'date',   // stays a Carbon date (no time)
        'start_time' => 'string', // TIME column -> keep as "HH:MM:SS"
        'end_time'   => 'string',
    ];

    public function collections()
    {
        return $this->hasMany(GownCollection::class);
    }

    public function getStartHmAttribute(): string
    {
        return $this->start_time
            ? \Carbon\Carbon::createFromFormat('H:i:s', $this->start_time)->format('H:i')
            : '';
    }

    public function getEndHmAttribute(): string
    {
        return $this->end_time
            ? \Carbon\Carbon::createFromFormat('H:i:s', $this->end_time)->format('H:i')
            : '';
    }

    public function getWindowLabelAttribute(): string
    {
        $d = optional($this->date)?->toDateString();
        $start = $this->start_time ? date('H:i', strtotime($this->start_time)) : null;
        $end   = $this->end_time   ? date('H:i', strtotime($this->end_time))   : null;

        return trim($d . ($start || $end ? " ({$start}–{$end})" : ''));
    }
}
