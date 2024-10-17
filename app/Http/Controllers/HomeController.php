<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    public static function index()
    {
        if (Auth::user() && Auth::user()->admin) {
            $users = User::getAllUsers();
        } else {
            $users = User::getVisibleUsers();
        }
        return view('callsigns', ['users' => $users]);
    }
}
