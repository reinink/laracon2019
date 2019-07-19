# Laracon US 2019

## 1. Implement view policy

Add new user policy (`php artisan make:policy UserPolicy`)

```php
public function view(User $user, User $other)
{
    return $user->club_id === $other->club_id || $user->buddies->contains($other);
}
```

```php
->get()
->filter(function ($user) {
    return Auth::user()->can('view', $user);
})
```

Create "visibleTo" User model scope:

```php
public function scopeVisibleTo($query, User $user)
{
    $query->where(function ($query) use ($user) {
        $query->where('club_id', $user->club_id)
            ->orWhereIn('id', $user->buddies->pluck('id'));
    });
}
```

```php
->visibleTo(Auth::user())
```

## 2. Sort by buddies first

```php
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
```

```php
->orderByBuddiesFirst(Auth::user())
```

## 3. Add last trip date

```php
<div class="w-1/3 px-3 pt-6 pb-3 text-2xl text-green-600 font-semibold">Last Trip</div>
<div class="w-1/3 px-3 py-4 text-gray-800">{{ $user->trips->sortByDesc('went_at')->first()->went_at->diffForHumans() }}</div>
```

```php
$users = User::with('club', 'trips')
```

```php
<div class="w-1/3 px-3 py-4 text-gray-800">{{ $user->trips()->latest('went_at')->first()->went_at->diffForHumans() }}</div>
```

```php
$users = User::with('club')
```

```php
public function scopeWithLastTripDate($query)
{
    $query->addSubSelect('last_trip_at', function ($query) {
        $query->select('went_at')
            ->from('trips')
            ->whereColumn('user_id', 'users.id')
            ->latest('went_at')
            ->limit(1);
    });
}
```

```php
->withLastTripDate()
```

```php
'last_trip_at' => 'datetime',
```

```php
<div class="w-1/3 px-3 py-4 text-gray-800">{{ $user->last_trip_at->diffForHumans() }}</div>
```

## 4. Add last trip lake

```php
public function scopeWithLastTripLake($query)
{
    $query->addSubSelect('last_trip_lake', function ($query) {
        $query->select('lake')
            ->from('trips')
            ->whereColumn('user_id', 'users.id')
            ->latest('went_at')
            ->limit(1);
    });
}
```

```php
->withLastTripLake()
```

```php
<div class="w-1/3 px-3 py-4 text-gray-800">
    {{ $user->last_trip_at->diffForHumans() }}
    <span class="text-sm text-gray-600">({{ $user->last_trip_lake }})</span>
</div>
```

Add dynamic relationship:

```php
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
```

```php
->withLastTrip()
```

```php
<div class="w-1/3 px-3 py-4 text-gray-800">
    <a class="hover:underline" href="/trips/{{ $user->lastTrip->id }}">
        {{ $user->lastTrip->went_at->diffForHumans() }}
    </a>
    <span class="text-sm text-gray-600">({{ $user->lastTrip->lake }})</span>
</div>
```
