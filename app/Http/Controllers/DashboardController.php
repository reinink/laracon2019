<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $users = User::query()
        ;
        return view('dashboard', ['users' => $users->paginate()]);
    }
}