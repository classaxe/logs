<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;

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
            'bands' =>      $data['bands'],
            'columns' =>    Log::columns,
            'lastPulled' => Carbon::parse($data['user']['qrz_last_data_pull'])->diffForHumans(),
            'modes' =>      $data['modes'],
            'user' =>       $data['user']
        ]);
    }
}
