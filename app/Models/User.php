<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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

    public function lastTrip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function scopeWithLastTrip($query)
    {
        $query->addSubSelect('last_trip_id', function ($query) {
            $query->select('id')
                ->from('trips')
                ->whereColumn('user_id', 'users.id')
                ->latest('went_at')
                ->limit(1);
        })->with('lastTrip');
    }
}
