<?php

use App\User;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    Auth::login(User::find(1)->load('club', 'buddies'));

    $users = User::with('club')
        ->orderBy('name')
        ->paginate(10);

    return view('users', ['users' => $users]);
});
