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

    public static function callsign(string $callsign)
    {
        $user = User::getUserByCallsign($callsign);
        $logs = Log::getLogsForUser($user);
        $bands = Log::getBandsForUserId($user['id']);
        $logs = array_reverse($logs);

//        print "<pre>" . print_r($logs[25], true) . "</pre>";

        return view('callsign', ['user' => $user, 'logs' => $logs, 'bands' => $bands]);
    }
}
