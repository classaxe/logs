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

        foreach ($logs as $i) {
            if ($i['mode'] !== 'FT8') {
                // continue;
            }
            $logs[] = [
                'date' =>   $i['date'],
                'time' =>   $i['time'],
                'call' =>   $i['call'],
                'band' =>   $i['band'],
                'mode' =>   $i['mode'],
                'rx' =>     $i['rx'],
                'tx' =>     $i['tx'],
                'pwr' =>    $i['pwr'],
                'qth' =>    $i['qth'],
                'sp' =>     $i['sp'],
                'itu' =>    $i['itu'],
                'gsq' =>    $i['gsq'],
                'km' =>     $i['km'],
                'conf' =>   $i['conf']
            ];
        }
        $logs = array_reverse($logs);
//        print "<pre>" . print_r($items[25], true) . "</pre>";
//        print "<pre>" . print_r($logs[25], true) . "</pre>";

        return view('callsign', ['user' => $user, 'logs' => $logs]);
    }
}
