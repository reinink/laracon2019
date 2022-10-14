<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    public function index()
    {
        \Illuminate\Support\Facades\Auth::login(User::find(1)->load('club', 'buddies'));

        $users = User::query()
            ->with('club')
//            ->withLastTrip()
//            ->visibleTo(Auth::user())
//            ->orderByBuddiesFirst(Auth::user())
//            ->orderBy('name')
            ->paginate(10);

        return view('users', ['users' => $users]);
    }
}