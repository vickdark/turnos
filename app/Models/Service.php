<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'duration_minutes',
        'capacity_per_slot',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
