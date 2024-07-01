<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;

class HomeController extends Controller
{
    public static function callsigns()
    {
        return view('callsigns', ['users' => User::getActiveUsers()]);
    }
}
