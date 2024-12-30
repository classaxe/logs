<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Potapark;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PotaparksController extends Controller
{
    public static function parkGrids(string $lat0, $lng0, $lat1, $lng1)
    {
        $parks = Potapark::getParks($lat0, $lng0, $lat1, $lng1);
        $data = [];
        foreach ($parks as $park) {
            $data[] = [
                'type' => 'Feature',
                'properties' => [
                    'reference' => $park['reference'],
                    'name' => $park['name']
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        (float)$park['lng'],
                        (float)$park['lat']
                    ]
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $data
        ], 200);
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
        $callsign = str_replace('-','/', $callsign);
        $data = User::getUserDataByCallsign($callsign);
        $q = [];
        if ($_GET['q'] ?? []) {
            foreach ($_GET['q'] as $qVal) {
                if (!$qVal) {
                    continue;
                }
                $keyval = explode('|', $qVal);
                if (count($keyval) === 2) {
                    $q[] = $keyval[0] . ": '" . $keyval[1] . "'";
                }
            }
        }
        return view('logs/logs', [
            'bands' =>      $data['bands'],
            'columns' =>    Log::COLUMNS,
            'gsqs' =>       $data['gsqs'],
            'lastPulled' => Carbon::parse($data['user']['qrz_last_data_pull'])->diffForHumans(),
            'modes' =>      $data['modes'],
            'q' =>          $q,
            'qths' =>       $data['qths'],
            'user' =>       $data['user']
        ]);
    }

    public static function logsStats(string $callsign, $mode) {
        $callsign = str_replace('-','/', $callsign);

        if (!$user = User::getUserByCallsign($callsign)) {
            return response()->json(['error' => true, 'data' => []], 404);
        }

        switch ($mode) {
            case 'usCounties':
                return response()->json([
                    'status' => 200,
                    'data' => Log::getLogUsStateCountiesForUser($user)
                ], 200);
            default:
                return response()->json(['error' => true, 'data' => []], 500);
        }
    }
}
