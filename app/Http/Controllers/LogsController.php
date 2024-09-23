<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LogsController extends Controller
{
    public static function logs(string $callsign)
    {
        $user = User::getUserByCallsign($callsign);
        $logs = Log::getLogsForUser($user);
        $user = User::getUserByCallsign($callsign);
        return response()->json([
            'status' => 200,
            'lastPulled' => $user->getLastQrzPull(),
            'logs' => $logs
        ]);
    }

    public static function logsFetch()
    {
        if (!Auth::user()) {
            return redirect()->route('login');
        }
        if (!Auth::user()->is_visible) {
            abort(403);
        }
        if (Log::getQRZDataForUser(Auth::user())) {
            return redirect()->route('logs.page', ['callsign' => Auth::user()->call]);
        }
        return redirect()->route('home')->with('status', '<b>Error:</b><br>' . Auth::user()->qrz_last_result);
    }

    public static function logsPage(string $callsign)
    {
        $data = User::getUserDataByCallsign($callsign);
        return view('logs/logs', [
            'bands' =>      $data['bands'],
            'columns' =>    Log::COLUMNS,
            'gsqs' =>       $data['gsqs'],
            'lastPulled' => Carbon::parse($data['user']['qrz_last_data_pull'])->diffForHumans(),
            'modes' =>      $data['modes'],
            'qths' =>       $data['qths'],
            'user' =>       $data['user']
        ]);
    }
}
