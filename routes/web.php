<?php

use App\User;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    Auth::login(User::find(1)->load('club', 'buddies'));

    $users = User::with('club')
        ->withLastTripDate()
        ->visibleTo(Auth::user())
        ->orderByBuddiesFirst(Auth::user())
        ->orderBy('name')
        ->paginate(10);

    return view('users', ['users' => $users]);
});
