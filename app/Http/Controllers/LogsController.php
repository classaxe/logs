<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;

class LogsController extends Controller
{
    public static function logs(string $callsign)
    {
        $user = User::getUserByCallsign($callsign);
        $logs = Log::getLogsForUser($user);
        $bands = Log::getBandsForUserId($user['id']);
        $modes = Log::getModesForUserId($user['id']);
        $logs = array_reverse($logs);
//        dd($modes);
//        print "<pre>" . print_r($logs[25], true) . "</pre>";

        return view('logs', ['user' => $user, 'logs' => $logs, 'bands' => $bands, 'modes' => $modes]);
    }
}
