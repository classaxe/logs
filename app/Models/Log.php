<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Adif\adif;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class Log extends Authenticatable
{
    use HasFactory, Notifiable;

    const MAXBATCHINSERT = 250;

    const APIURL = "https://logbook.qrz.com/api";

    const COLUMNS = [
        'logNum' =>     ['lbl' =>   'Log',          'class' => ''],
        'myQsq' =>      ['lbl' =>   'My GSQ',       'class' => 'not-compact multi-qth'],
        'myQth' =>      ['lbl' =>   'My QTH',       'class' => 'not-compact multi-qth'],
        'date' =>       ['lbl' =>   'Date',         'class' => ''],
        'time' =>       ['lbl' =>   'UTC',          'class' => ''],
        'call' =>       ['lbl' =>   'Callsign',     'class' => ''],
        'name' =>       ['lbl' =>   'Name',         'class' => 'not-compact'],
        'band' =>       ['lbl' =>   'Band',         'class' => ''],
        'mode' =>       ['lbl' =>   'Mode',         'class' => ''],
        'rx' =>         ['lbl' =>   'RX',           'class' => 'r'],
        'tx' =>         ['lbl' =>   'TX',           'class' => 'r'],
        'pwr' =>        ['lbl' =>   'Pwr',          'class' => 'r'],
        'qth' =>        ['lbl' =>   'Location',     'class' => ''],
        'countyName' => ['lbl' =>   'US County',    'class' => 'not-compact'],
        'sp' =>         ['lbl' =>   'S/P',          'class' => ''],
        'itu' =>        ['lbl' =>   'Country',      'class' => ''],
        'continent' =>  ['lbl' =>   'Cont',         'class' => ''],
        'gsq' =>        ['lbl' =>   'GSQ',          'class' => ''],
        'km' =>         ['lbl' =>   'Km',           'class' => 'r'],
        'deg' =>        ['lbl' =>   'Deg',          'class' => 'r not-compact'],
        'conf' =>       ['lbl' =>   'Conf',         'class' => 'r']
    ];
    const GSQ_SUBSTITUTES = [
        3 => [
            'VK0IR' => 'MD66' // Heard Island operator who placed wrong GSQ in Antarctica mainland
        ]
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'qrzId',
        'userId',
        'date',
        'time',
        'call',
        'name',
        'band',
        'mode',
        'rx',
        'tx',
        'pwr',
        'qth',
        'county',
        'sp',
        'itu',
        'continent',
        'gsq',
        'km',
        'deg',
        'conf'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [];
    }

    public static function convertGsqToDegrees($GSQ) {
        $GSQ =      substr(strToUpper($GSQ), 0, 6);
        $offset =   (strlen($GSQ)==6 ? 1/48 : 0);
        if (strlen($GSQ) == 4) {
            $GSQ = $GSQ."MM";
        }
        if (!preg_match('/^[a-rA-R]{2}[0-9]{2}([a-xA-X]{2})?$/i', $GSQ)) {
            return false;
        }
        $lon_d = ord(substr($GSQ, 0, 1))-65;
        $lon_m = substr($GSQ, 2, 1);
        $lon_s = ord(substr($GSQ, 4, 1))-65;

        $lat_d = ord(substr($GSQ, 1, 1))-65;
        $lat_m = substr($GSQ, 3, 1);
        $lat_s = ord(substr($GSQ, 5, 1))-65;

        return [
            "lat" => (int)round(($lat_d*10 + $lat_m + $lat_s/24 + $offset - 90)*10000)/10000,
            "lon" => (int)round((2 * ($lon_d*10 + $lon_m + $lon_s/24 + $offset) - 180)*10000)/10000
        ];
    }

    public static function getBearing($qthGSQ, $logGSQ) {
        $qth = static::convertGsqToDegrees($qthGSQ);
        $log = static::convertGsqToDegrees($logGSQ);
        if ($qth === false || $log === false) {
            return null;
        }
        if ($qth['lat'] === $log['lat'] && $qth['lon'] === $log['lon']) {
            return 0;
        }
        $qth_lat_r =    deg2rad($qth['lat']);
        $qth_lon_r =    deg2rad($qth['lon']);
        $log_lat_r =    deg2rad($log['lat']);
        $log_lon_r =    deg2rad($log['lon']);
        $diff_lon =     ($log['lon'] - $qth['lon']);
        if (abs($diff_lon) > 180) {
            $diff_lon = (360 - abs($diff_lon)) * (0 - ($diff_lon / abs($diff_lon)));
        }
        $diff_lon_r =   deg2rad($diff_lon);
        $deg = (
            rad2deg(
                atan2(
                    SIN($log_lon_r - $qth_lon_r) * COS($log_lat_r),
                    COS($qth_lat_r) * SIN($log_lat_r) - SIN($qth_lat_r) * COS($log_lat_r) * COS($diff_lon_r)
                )
            ) + 360
        ) % 360;
//        dump([$qth, $log, $deg]);
        return $deg;
    }

    public static function getQRZStatusForUser(User $user)
    {
        try {
            $url = self::APIURL . '?KEY=' . $user['qrz_api_key'] . '&ACTION=STATUS';
            $raw = file_get_contents($url);
//            print $url ."\n";
//            print $raw . "\n";
        } catch (\Exception $e) {
            return false;
        }
        $status = [];
        $pairs = explode('&', $raw);
        foreach ($pairs as $pair) {
            list($key, $value) = explode('=', $pair, 2);
            $status[$key] = $value;
        }
        if ($status['RESULT'] === "OK") {
            return $status;
        }
        if (str_contains($status['REASON'], 'invalid api key')) {
            $user->setAttribute('qrz_last_result', 'Invalid QRZ Key');
            $user->save();
            return false;
        }
        if (str_contains($status['REASON'], 'user does not have a valid QRZ subscription')) {
            $user->setAttribute('qrz_last_result', 'Not XML Subscriber');
            $user->save();
            return false;
        }
        if ($status['CALLSIGN'] !== $user->call) {
            $user->setAttribute('qrz_last_result', 'Wrong Call for key');
            $user->save();
            return false;
        }
        $user->setAttribute('qrz_last_result', 'QRZ Server error');
        $user->save();
        return false;
    }

    public static function getQRZDataForUser(User $user)
    {
        ini_set('max_execution_time', 600);
        $status = self::getQRZStatusForUser($user);
        if (!$status) {
            return;
        }
        $qthNames = [];
        if ($user['qth_names']) {
            $qthNamesTemp =     explode("\r\n", $user['qth_names']);
            foreach ($qthNamesTemp as $qthName) {
                $bits = explode('=', $qthName);
                if (isset($bits[1])) {
                    $qthNames[trim($bits[0])] = trim($bits[1]);
                }
            }
        }
        // https://logbook.qrz.com/api?KEY=YOURQRZAPIKEY&ACTION=FETCH&OPTION=MODSINCE:2024-08-18
        try {
            $url = self::APIURL . '?KEY=' . $user['qrz_api_key'] . '&ACTION=FETCH&OPTION=ALL';
            $raw = file_get_contents($url);
        } catch (\Exception $e) {
            $user->setAttribute('qrz_last_result', 'Server Error - ' . substr($e->getMessage(), 0, 240));
            $user->save();
            return false;
        }
        if (!(substr($raw, 0, 5) === 'ADIF=' || str_contains($raw, 'RESULT=OK'))) {
            $user->setAttribute('qrz_last_result', substr($raw, 0, 250));
            $user->save();
            return false;
        }
        $data = str_replace(['&lt;', '&gt;'], ['<', '>'], $raw);

        $adif = new adif(trim('<EOH>' . $data));
        $qrzItems = $adif->parser();
        $items = [];
        foreach ($qrzItems as $i) {
            if (!isset($i['APP_QRZLOG_LOGID'])) {
                continue;
            }
            try {
                $itu =      $i['COUNTRY'];
                $sp =       $i['STATE'] ?? '';
                $county =   $i['CNTY'] ?? '';
                switch ($itu) {
                    case 'Australia':
                    case 'Canada':
                        $county = '';
                        break;
                    case 'Alaska':
                    case 'Hawaii':
                    case 'Puerto Rico':
                    case 'US Virgin Islands':
                        break;
                    case 'United States':
                        $itu = 'USA';
                        break;
                    default:
                        $sp = '';
                        $county = '';
                        break;
                }
                $log_gsq =          strtoupper($i['GRIDSQUARE'] ?? '');
                if (isset(self::GSQ_SUBSTITUTES[$user['id']][$i['CALL']])) {
                    $log_gsq = self::GSQ_SUBSTITUTES[$user['id']][$i['CALL']];
                }
                $my_gsq =           strtoupper(substr($i['MY_GRIDSQUARE'] ?? $user['gsq'], 0, 6));
                $my_qth =           $i['MY_CITY'] ?? $user['qth'];
                if (isset($qthNames[$my_gsq])) {
                    $my_qth = $qthNames[$my_gsq];
                }
                $deg =              Log::getBearing($my_gsq, $log_gsq);
                $name =             $i['NAME'] ?? '';
                $name =             (strtoupper($name) === $name ? ucwords(strtolower($name)) : $name);

                $items[] = [
                    'logNum' =>     0,
                    'userId' =>     $user['id'],
                    'qrzId' =>      $i['APP_QRZLOG_LOGID'],
                    'myGsq' =>      $my_gsq,
                    'myQth' =>      $my_qth,
                    'date' =>       substr($i['QSO_DATE'], 0, 4) . '-' . substr($i['QSO_DATE'], 4, 2) . '-' . substr($i['QSO_DATE'], 6, 2),
                    'time' =>       substr($i['TIME_ON'], 0, 2) . ':' . substr($i['TIME_ON'], 2, 2),
                    'call' =>       $i['CALL'],
                    'name' =>       $name,
                    'band' =>       strtolower($i['BAND']),
                    'mode' =>       $i['MODE'] ?? '',
                    'rx' =>         $i['RST_RCVD'] ?? '',
                    'tx' =>         $i['RST_SENT'] ?? '',
                    'pwr' =>        $i['TX_PWR'] ?? '',
                    'qth' =>        $i['QTH'] ?? '',
                    'county' =>     $county,
                    'sp' =>         $sp,
                    'itu' =>        $itu,
                    'continent' =>  strtoupper($i['CONT'] ?? ''),
                    'gsq' =>        substr(strtoupper($log_gsq), 0, 4),
                    'km' =>         $i['DISTANCE'] ?? null,
                    'deg' =>        $deg,
                    'conf' =>       ($i['APP_QRZLOG_STATUS'] ?? '') === 'C' ? 'Y' : ''
                ];
            } catch (\Exception $e) {
                $user->setAttribute('qrz_last_result', 'Server Error - ' . substr($e->getMessage(), 0, 240));
                $user->save();
                return false;
            }
        }
        if ($items) {
            // To deal with logs uploaded to QRZ out of order
            usort($items, function ($a, $b) {
                return $a['date'] . $a['time'] <=> $b['date'] . $b['time'];
            });
            $lognum = 1;
            $qths = [];
            foreach ($items as &$item) {
                $item['logNum'] = $lognum++;
                if (!isset($qths[$item['myQth']])) {
                    $qths[$item['myQth']] = true;
                }
            }

            try {
                Log::deleteLogsForUserId($user->id);
                //log::insert($items);
                for ($i=0; $i<count($items); $i += self::MAXBATCHINSERT) {
                    $chunk = array_slice($items, $i, self::MAXBATCHINSERT);
                    Log::insert($chunk);
                }

                $first = Log::where('userId','=',$user->id)->orderBy('date', 'asc')->orderBy('time', 'asc')->first();
                $last = Log::where('userId','=',$user->id)->orderBy('date', 'desc')->orderBy('time', 'desc')->first();
                $qthsForUser = Log::getLogQthsForUser($user->id);
                $qthNames = implode(
                    "\r\n",
                    array_map(
                        function ($item) {
                            return $item['myGsq'] . ' = ' .$item['myQth'];
                        },
                        $qthsForUser
                    )
                );

                $user->setAttribute('qrz_last_result', 'OK');
                $user->setAttribute('qrz_last_data_pull', time());
                $user->setAttribute('first_log', $first->date . ' ' . $first->time);
                $user->setAttribute('last_log', $last->date . ' ' . $last->time);
                $user->setAttribute('log_count', count($items));
                $user->setAttribute('qth_count', count(array_keys($qths)));
                $user->setAttribute('qth_names', $qthNames);
                $user->save();
                return true;
            } catch (\Exception $e) {
                $user->setAttribute('qrz_last_result', 'Server Error - ' . substr($e->getMessage(), 0, 240));
                $user->save();
                return false;
            }
        }
        return false;
    }

    public static function getLogsForUser(User $user): array
    {
        if ($user->active && (
            !$user->qrz_last_data_pull
            || $user->qrz_last_data_pull->addMinutes(getEnv('LOGS_MAX_AGE'))->isPast()
        )) {
            static::getQRZDataForUser($user);
        }
        return Log::getDBLogsForUserId($user->id);
    }

    public static function getLogQthsForUser($userId): array
    {
        return
            Log::Select(
                'logs.myGsq',
                'logs.myQth'
            )
            ->groupBy('myGsq','myQth')
            ->where('userId', $userId)
            ->orderBy('myGsq', 'asc')
            ->orderBy('myQth', 'asc')
            ->get()
            ->toArray();
    }

    public static function getBandsForUserId($userId): array
    {
        $items = Log::distinct()->where('userId', $userId)->get(['band'])->toArray();
        $out = [];
        foreach ($items as $item) {
            $b =        $item['band'];
            $num =      preg_replace('/[^0-9]/', '', $b);
            $units =    preg_replace('/[^a-zA-Z]/', '', $b);
            $v =        ($num ? $num * (strtolower($units) === 'm' ? 1000 : 1) : $b);
            $out[$v] =  $item['band'];
        }
        krsort($out);
        return array_values($out);
    }

    public static function getGsqsForUserId($userId): array
    {
        $items = Log::distinct()->where('userId', $userId)->get(['myGsq'])->toArray();
        $out = [];
        foreach($items as $item) {
            $out[] = $item['myGsq'];
        }
        sort($out);
        return $out;
    }

    public static function getModesForUserId($userId): array
    {
        $items = Log::distinct()->where('userId', $userId)->get(['mode'])->toArray();
        $out = [];
        foreach($items as $item) {
            $out[] = $item['mode'];
        }
        sort($out);
        return $out;
    }

    public static function getQthsForUserId($userId): array
    {
        $items = Log::selectRaw('count(*) as num, myQth')->where('userId', $userId)->groupBy('myQth')->get()->toArray();
        $out = [];
        foreach($items as $item) {
            $out[$item['myQth']] = $item['num'];
        }
        ksort($out);
        return $out;
    }

    public static function getDBLogsForUserId($userId): array
    {
        return
            Log::Select(
                'logs.band',
                'logs.call',
                'logs.conf',
                'logs.continent',
                'logs.county',
                'logs.date',
                'logs.deg',
                'logs.gsq',
                'logs.itu',
                'logs.km',
                'logs.logNum',
                'logs.mode',
                'logs.myGsq',
                'logs.myQth',
                'logs.name',
                'logs.pwr',
                'logs.qth',
                'logs.rx',
                'logs.sp',
                'logs.time',
                'logs.tx',
                'iso3166.flag'
            )->leftJoin('iso3166', 'logs.itu', '=', 'iso3166.country')
            ->where('userId', $userId)
            ->orderBy('time', 'asc')
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
    }

    public static function deleteLogsForUserId($userId): bool
    {
        return Log::where('userId', $userId)->delete();
    }
}
