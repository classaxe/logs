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

    const columns = [
        'logNum' =>     ['lbl' =>   'Log',      'class' => ''],
        'date' =>       ['lbl' =>   'Date',     'class' => ''],
        'time' =>       ['lbl' =>   'UTC',      'class' => ''],
        'call' =>       ['lbl' =>   'Callsign', 'class' => ''],
        'band' =>       ['lbl' =>   'Band',     'class' => ''],
        'mode' =>       ['lbl' =>   'Mode',     'class' => ''],
        'rx' =>         ['lbl' =>   'RX',       'class' => 'r'],
        'tx' =>         ['lbl' =>   'TX',       'class' => 'r'],
        'pwr' =>        ['lbl' =>   'Pwr',      'class' => 'r'],
        'qth' =>        ['lbl' =>   'Location', 'class' => ''],
        'countyName' => ['lbl' =>   'US County', 'class' => ''],
        'sp' =>         ['lbl' =>   'S/P',      'class' => ''],
        'itu' =>        ['lbl' =>   'Country',  'class' => ''],
        'continent' =>  ['lbl' =>   'Cont',     'class' => ''],
        'gsq' =>        ['lbl' =>   'GSQ',      'class' => ''],
        'km' =>         ['lbl' =>   'Km',       'class' => 'r'],
        'conf' =>       ['lbl' =>   'Conf',     'class' => 'r']
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
    protected function casts(): array
    {
        return [
        ];
    }

    public static function getQRZDataForUser(User $user)
    {
        try {
            $url = 'https://logbook.qrz.com/api?KEY=' . $user['qrz_api_key'] . '&ACTION=FETCH&OPTION=ALL';
            $raw = file_get_contents($url);
        } catch (\Exception $e) {
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
                $items[] = [
                    'logNum' =>     $logNum,
                    'userId' =>     $user['id'],
                    'qrzId' =>      $i['APP_QRZLOG_LOGID'],
                    'date' =>       substr($i['QSO_DATE'], 0, 4) . '-' . substr($i['QSO_DATE'], 4, 2) . '-' . substr($i['QSO_DATE'], 6, 2),
                    'time' =>       substr($i['TIME_ON'], 0, 2) . ':' . substr($i['TIME_ON'], 2, 2),
                    'call' =>       $i['CALL'],
                    'band' =>       $i['BAND'],
                    'mode' =>       $i['MODE'] ?? '',
                    'rx' =>         $i['RST_RCVD'] ?? '',
                    'tx' =>         $i['RST_SENT'] ?? '',
                    'pwr' =>        $i['TX_PWR'] ?? '',
                    'qth' =>        $i['QTH'] ?? '',
                    'county' =>     $county,
                    'sp' =>         $sp,
                    'itu' =>        $itu,
                    'continent' =>  strtoupper($i['CONT'] ?? ''),
                    'gsq' =>        (isset($i['GRIDSQUARE']) ? strtoupper(substr($i['GRIDSQUARE'], 0, 4)) : ''),
                    'km' =>         $i['DISTANCE'] ?? null,
                    'conf' =>       ($i['APP_QRZLOG_STATUS'] ?? '') === 'C' ? 'Y' : ''
                ];
                $logNum++;
            } catch (\Exception $exception) {
                print $exception->getMessage();
                dd($i);
            }
        }
        if ($items) {
            Log::deleteLogsForUserId($user->id);
            Log::insert($items);
            $user->setAttribute('qrz_last_data_pull', time());
            $user->setAttribute('log_count', count($items));
            $user->save();
            return true;
        }
        return false;
    }

    public static function getLogsForUser(User $user): array
    {
        if (!$user->qrz_last_data_pull || $user->qrz_last_data_pull->addMinutes(60)->isPast()) {
            static::getQRZDataForUser($user);
        }
        return Log::getDBLogsForUserId($user->id);
    }

    public static function getBandsForUserId($userId): array
    {
        $bands = Log::distinct()->where('userId', $userId)->get(['band'])->toArray();
        $out = [];
        foreach ($bands as $band) {
            $b = $band['band'];
            $num =      preg_replace('/[^0-9]/', '', $b);
            $units =    preg_replace('/[^a-zA-Z]/', '', $b);
            $value =    $num * ($units === 'm' ? 1000 : 1);
            $out[$value] = $band['band'];
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
