# Laravel Talk based on [Jonathan Reinink - Eloquent Performance Patterns (Laracon 2019)](https://www.youtube.com/watch?v=IBUXXErAtuk&t=1020s)

## What about the product (it's just an MVP)

We have a social media oriented to people that love fishing. In our app people have a user, belongs to a club and have
several friends.
And also, as user you can save details of trips that made for fishing (when was and at where).

## What about the tech side

* Laravel 9
* Breeze
* Sail
* LaravelDebugBar

## What about the data

```sql
Select (select count(1) from users)   users,
       (select count(1) from friends) friends,
       (select count(1) from trips)   trips
  ```

## Client's requirements

## 1. What a user can see

An user should only be able to see people on the same club and also own friends.

* Alternative a): implements a policy and
  follow [Authorization](https://laravel.com/docs/9.x/authorization#main-content) strategy of laravel

  Add new user policy (`sail artisan make:policy UserPolicy`)

```php
public function view(User $user, User $other)
{
    return $user->club_id === $other->club_id || $user->buddies->contains($other);
}
```

    Meanwhile in the controller

```php
->get()
->filter(function ($user) {
    return Auth::user()->can('view', $user);
})
```

    Note: Dashboard contoller has a call to `paginate` method. This method doesnt exists on collections. Look AppServiceProvider to see the 
    macro that allows to keep the signature for this example.

Create "visibleTo" User model scope:

* Alternative b) Implement some sort of baseAccessControl (like in common core) in there we could get the list of
  visible users already filtered

```php
public function scopeVisibleTo($query, User $user)
{
    $query->where(function ($query) use ($user) {
        $query->where('club_id', $user->club_id)
            ->orWhereIn('id', $user->friends()->select('friend_id'));
    });
}
```

    Meanwhile in the controller

```php
->visibleTo(Auth::user())
```

## 2. Show the club name and mark friends in the list

For every user show the name of the club where it belongs to; and also a user can see a lot of members, so it should be
able to
distinguish who is a friend and who it's just a member of the same club.

* a) Club name: use [eager loading](https://laravel.com/docs/9.x/eloquent-relationships#eager-loading) vs lazy loading

```html
<!-- header -->
<div class="w-1/3 px-3 pt-6 pb-3 text-2xl text-indigo-600 font-semibold">Club</div>

<!-- row -->
<div class="w-1/3 px-3 py-4 text-gray-800">{{ $user->club->name }}</div>

```

* b) A label to mark if user in the list is a friend: this is the same thing but more complex in terms of performance.
* First approach it's to solve this in the blade.

```php
@if (Auth::user()->friends->contains($user))
    <div class="ml-2 px-2 py-1 text-xs text-yellow-800 font-semibold bg-yellow-500 rounded-full">
        Friend
    </div>
@endif
```

* Second approach it's let the SQL side to solve this problem for us. First thing it's to be clear what we want to show.
* The goal is to mark when a user is part of the list of friends of a particular user.
  In other words, if exists a relation between a some user (user displayed in the list) and the particular one
  user (`Auth::user()`).

```php
/**
 * This can be read as: the friends table has two keys. The user_id key points to the owner of the relation,
 * somethings called parent (who says: this is my list of friends). 
 * And the second key points the the child or the belonged side (who says I am part of the list that belongs to who is identified by the first key)
 */
   public function friends()
  {
      return $this->belongsToMany(related: User::class, table: 'friends', foreignPivotKey: 'user_id', relatedPivotKey: 'friend_id')->withTimestamps();
  }


```

We could write a sql constraint that allow us to say: for every user add a column that indicates if is friend of some
user:

```php
public function scopeWithIsFriendOfUser($query, User $user)
{
    // add a new column to the result set 
    $query->addSelect([
        'is_friend_of_user' => Friend::query()
            // if count gives us a value bigger than one, it means that that particular row (aka user) is friend of the user (parameter)
            // buddies [user_id, buddy_id].
            ->selectRaw('count(1)')
            // if the user is friend (second part of the relation) of ...
            ->whereColumn(first: 'users.id', operator: '=', second: 'friends.friend_id')
            // the owner of this relation (first part of the relation)
            ->where('friends.user_id', $user->id)
    ]);
}
```

```php
    // In the model casts attribute
  'is_friend_of_user' => 'boolean',
```

In the blade

```php
@if ($user->is_friend_of_user)
    <div class="ml-2 px-2 py-1 text-xs text-yellow-800 font-semibold bg-yellow-500 rounded-full">
        Friend
    </div>
@endif

```

```php
    // In the controller
    ->withIsFriendOfUser(Auth::user())
```

* First alternative it's to show this info in the view by using the list of friends (eager/lazy loading)

## 3. Sort by friends first

```php
 public function scopeOrderByFriendsFirst($query, User $user)
{
    $query->orderBy(function ($query) use ($user) {
        $query
            ->from('friends')
            ->selectRaw('true')
            ->whereColumn(first: 'friends.friend_id', operator: '=', second: 'users.id')
            ->where('user_id', $user->id)
            ->limit(1);
    }, 'asc');
}
```

```php
->orderByBuddiesFirst(Auth::user())
```

## 4. Add last trip date

```php
<div class="w-1/3 px-3 pt-6 pb-3 text-2xl  text-indigo-600 font-semibold">Last Trip</div>
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
    $query->addSelect(['last_trip_at' =>
        Trip::query()
            ->select('went_at')
            ->from('trips')
            ->whereColumn('user_id', 'users.id')
            ->latest('went_at')
            ->limit(1)
    ]);
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

## 5. Add last trip lake

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
    // In this case, laravel assumes that user's table has a column named last_trip_id
    // we will make laravel believes that columns exists by using a subquery
    return $this->belongsTo(Trip::class);
}

public function scopeWithLastTrip($query)
{
    $query->addSelect(['last_trip_id' => Trip::query()
        ->select('id')
        ->from('trips')
        ->whereColumn('user_id', 'users.id')
        ->latest('went_at')
        ->limit(1)
    ])->with('lastTrip');
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
