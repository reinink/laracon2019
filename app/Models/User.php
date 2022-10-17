<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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


    /**
     * @param Builder $query
     * @param User $user
     * @return void
     */
    public function scopeVisibleTo($query, User $user)
    {
        // select * from employees
        $query->where(function ($query) use ($user) {
            $query->where('club_id', $user->club_id)
                ->orWhereIn('id', $user->friends()->select('friend_id'));
        });
    }

    /**
     * @param Builder $query
     * @param User $user
     * @return void
     */
    public function scopeWithIsFriendOfUser($query, User $user)
    {
        $query->addSelect([
            'is_friend_of_user' => Friend::query()
                // if count gives us a value bigger than one, it means that that particular row (aka user) is friend of the user (parameter)
                // buddies [user_id, buddy_id].
                ->selectRaw('count(1)')
                // users.id is visible because come in $query object
                ->whereColumn(first: 'users.id', operator: '=', second: 'friends.friend_id')
                ->where('friends.user_id', $user->id)
        ]);
    }
}
