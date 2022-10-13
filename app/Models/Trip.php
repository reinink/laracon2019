<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $casts = [
        'went_at' => 'datetime',
    ];
}
