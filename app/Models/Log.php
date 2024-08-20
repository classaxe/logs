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

    const MAX_AGE = 60;
    const MAXBATCHINSERT = 250;

    const APIURL = "https://logbook.qrz.com/api";

    const COLUMNS = [
        'logNum' =>     ['lbl' =>   'Log',          'class' => ''],
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
        // $user['qrz_api_key'] = "eahgwhwtyhjetyjetj"; // Invalid key
        // $user['qrz_api_key'] = "8A43-EAFF-BD8E-857F"; // Non XML Subscriber
        try {
            $url = self::APIURL . '?KEY=' . $user['qrz_api_key'] . '&ACTION=STATUS';
            $raw = file_get_contents($url);
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
            $user->setAttribute('qrz_last_result', 'Invalid QRZ API Key');
            $user->save();
            return false;
        }
        if (str_contains($status['REASON'], 'user does not have a valid QRZ subscription')) {
            $user->setAttribute('qrz_last_result', 'Not XML Subscriber');
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
        // https://logbook.qrz.com/api?KEY=YOURQRZAPIKEY&ACTION=FETCH&OPTION=MODSINCE:2024-08-18
        try {
            $url = self::APIURL . '?KEY=' . $user['qrz_api_key'] . '&ACTION=FETCH&OPTION=ALL';
            $raw = file_get_contents($url);
        } catch (\Exception $e) {
            $user->setAttribute('qrz_last_data_pull', null);
            $user->setAttribute('qrz_last_result', 'Server Error');
            $user->save();
            return false;
        }
        if (substr($raw, 0, 5) === 'ADIF=' || str_contains($raw, 'RESULT=OK')) {
            $user->setAttribute('qrz_last_result', 'OK');
        } else {
            try {
                if (str_contains($raw, 'REASON=user does not have a valid QRZ subscription')) {
                    $user->setAttribute('qrz_last_result', 'Not XML Subscriber');
                } elseif (str_contains($raw, 'REASON=invalid api key')) {
                    $user->setAttribute('qrz_last_result', 'Invalid QRZ API Key');
                } else {
                    $user->setAttribute('qrz_last_result', substr($raw, 0, 100));
                }
                $user->save();
            } catch (\Exception $e) {
                print substr($e->getMessage(), 0, 100);
                return false;
            }
            return false;
        }
        $data = str_replace(['&lt;', '&gt;'], ['<', '>'], $raw);

        $adif = new adif(trim('<EOH>' . $data));
        $qrzItems = $adif->parser();
        $items = [];
        $logNum = 1;
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
                $log_gsq =          $i['GRIDSQUARE'] ?? '';
                $qth_gsq =          $i['MY_GRIDSQUARE'] ?? $user['gsq'];
                $deg =              Log::getBearing($qth_gsq, $log_gsq);
                $name =             $i['NAME'] ?? '';
                $name =             (strtoupper($name) === $name ? ucwords(strtolower($name)) : $name);
                $items[] = [
                    'logNum' =>     $logNum,
                    'userId' =>     $user['id'],
                    'qrzId' =>      $i['APP_QRZLOG_LOGID'],
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
                $logNum++;
            } catch (\Exception $e) {
                $user->setAttribute('qrz_last_data_pull', null);
                $user->setAttribute('qrz_last_result', $e->getMessage());
                $user->save();
                return false;
            }
        }
        if ($items) {
            try {
                Log::deleteLogsForUserId($user->id);
                //log::insert($items);
                for ($i=0; $i<count($items); $i += self::MAXBATCHINSERT) {
                    $chunk = array_slice($items, $i, self::MAXBATCHINSERT);
                    Log::insert($chunk);
                }
                $last = Log::where('userId','=',$user->id)->orderBy('date', 'desc')->orderBy('time', 'desc')->first();
                $user->setAttribute('qrz_last_data_pull', time());
                $user->setAttribute('last_log', $last->date . ' ' . $last->time);
                $user->setAttribute('log_count', count($items));
                $user->save();
                return true;
            } catch (\Exception $e) {
                $user->setAttribute('qrz_last_result', $e->getMessage());
                $user->save();
                return false;
            }
        }
        return false;
    }

    public static function getLogsForUser(User $user): array
    {
        if (!$user->qrz_last_data_pull || $user->qrz_last_data_pull->addMinutes(self::MAX_AGE)->isPast()) {
            static::getQRZDataForUser($user);
        }
        return Log::getDBLogsForUserId($user->id);
    }

    public static function getBandsForUserId($userId): array
    {
        $bands = Log::distinct()->where('userId', $userId)->get(['band'])->toArray();
        $out = [];
        foreach ($bands as $band) {
            $b =        $band['band'];
            $num =      preg_replace('/[^0-9]/', '', $b);
            $units =    preg_replace('/[^a-zA-Z]/', '', $b);
            $v =        ($num ? $num * (strtolower($units) === 'm' ? 1000 : 1) : $b);
            $out[$v] =  $band['band'];
        }
        krsort($out);
        return array_values($out);
    }

    public static function getModesForUserId($userId): array
    {
        $modes = Log::distinct()->where('userId', $userId)->get(['mode'])->toArray();
        $out = [];
        foreach($modes as $mode) {
            $out[] = $mode['mode'];
        }
        sort($out);
        return $out;
    }

    public static function getDBLogsForUserId($userId): array
    {
        return Log::where('userId', $userId)->get()->toArray();
    }

    public static function deleteLogsForUserId($userId): bool
    {
        return Log::where('userId', $userId)->delete();
    }
}
