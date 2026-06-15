<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'phone',
        'first_name',
        'last_name',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
