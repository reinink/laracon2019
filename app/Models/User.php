<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property integer id
 * @property integer club_id
 * @property Collection buddies
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_friend_of_user' => 'boolean',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function friends()
    {
        return $this->belongsToMany(related: User::class, table: 'friends', foreignPivotKey: 'user_id', relatedPivotKey: 'friend_id')->withTimestamps();
    }
}
