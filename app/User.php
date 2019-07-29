<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function buddies()
    {
        return $this->belongsToMany(User::class, 'buddies', 'user_id', 'buddy_id')->withTimestamps();
    }

    public function scopeVisibleTo($query, User $user)
    {
        $query->where(function ($query) use ($user) {
            $query->where('club_id', $user->club_id)
                ->orWhereIn('id', $user->buddies->pluck('id'));
        });
    }

    public function scopeOrderByBuddiesFirst($query, User $user)
    {
        $query->orderBySub(function ($query) use ($user) {
            $query->selectRaw('true')
                ->from('buddies')
                ->whereColumn('buddies.buddy_id', 'users.id')
                ->where('user_id', $user->id)
                ->limit(1);
        });
    }
}
