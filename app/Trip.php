<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $casts = [
        'went_at' => 'datetime',
    ];
}
