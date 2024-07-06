<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;

class LogsController extends Controller
{
    public static function logs(string $callsign)
    {
        $user = User::getUserByCallsign($callsign);
        return response()->json([
            'status' => 200,
            'logs' => Log::getLogsForUser($user)
        ]);
    }

    public static function logsPage(string $callsign)
    {
        $data = User::getUserDataByCallsign($callsign);
        return view('logs', [
            'user' =>   $data['user'],
            'bands' =>  $data['bands'],
            'modes' =>  $data['modes']
        ]);
    }
}
