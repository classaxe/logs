<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    const RECENTDAYS = 30;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gsq',
        'qth',
        'city',
        'sp',
        'itu',
        'call',
        'qrz_api_key',
        'qrz_last_data_pull',
        'qth_names',
        'is_visible',
        'log_count',
        'clublog_email',
        'clublog_password',
        'clublog_call',
        'clublog_last_data_pull',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'qrz_last_data_pull' => 'datetime',
        'clublog_last_data_pull' => 'datetime',
    ];

    private static function calculateEnclosingCircle($points) {
        if (count($points) === 0) {
            return null;
        }
        // Horrible kludgey hack because the welzl routine doesn't seem to work well beyond 3 points.
        if (count($points) <= 3) {
            $pointArr = [];
            foreach ($points as $point) {
                $pointArr[] = new Point($point['lon'], $point['lat']);
            }

            $circle = SmallestEnclosingCircle::makeCircle($pointArr);
            $center = $circle->getCenter();
            return [
                'center' => [$center->getY(), $center->getX()],
                'radius' => $circle->getRadius() * 99500
            ];
        }
        $latSum = 0;
        $lonSum = 0;
        foreach ($points as $point) {
            $latSum += $point['lat'];
            $lonSum += $point['lon'];
        }
        $centerLat = $latSum / count($points);
        $centerLon = $lonSum / count($points);
        $maxDistance = 0;
        foreach ($points as $point) {
            $distance = self::calculateDX($centerLat, $centerLon, $point['lat'], $point['lon']);
            if ($distance > $maxDistance) {
                $maxDistance = $distance;
            }
        }
        return [
            'center' => [$centerLat, $centerLon],
            'radius' => $maxDistance // in meters
        ];
    }

    private static function calculateDX($latFrom, $lonFrom, $latTo, $lonTo, $earthRadius = 6371000) {
// Convert from degrees to radians
        $latFrom = deg2rad($latFrom);
        $lonFrom = deg2rad($lonFrom);
        $latTo = deg2rad($latTo);
        $lonTo = deg2rad($lonTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in meters
    }

    public function delete() {
        DB::statement("DELETE FROM `logs` WHERE `userId` = " . $this->id);
        return parent::delete();
    }
    /**
     * @return array
     */
    public static function getVisibleUsers(): Collection
    {
        return User::where('is_visible', 1)->orderBy('call', 'asc')->get();
    }

    public static function getActiveUsers(): Collection
    {
        return User::where('active', 1)->orderBy('call', 'asc')->get();
    }

    public static function getAllUsers(): Collection
    {
        return User::orderBy('call', 'asc')->get();
    }

    public static function getAllUserItus(): array
    {
        return User::select('itu')->groupBy('itu')->orderBy('itu', 'asc')->pluck('itu')->toArray();
    }

    public static function getClublogUsers($call = null): Collection
    {
        if ($call) {
            return User::where('clublog_email', '<>', '')
                ->where('clublog_password', '<>', '')
                ->whereColumn('clublog_call', 'call')
                ->where('call', $call)
                ->get();
        }
        return User::where('clublog_email', '<>', '')
            ->where('clublog_password', '<>', '')
            ->whereColumn('clublog_call', 'call')
            ->orderBy('call', 'asc')
            ->get();
    }

    public static function array_column_pair(array $inputArray, string $column1, string $column2): array
    {
        $result = [];
        foreach ($inputArray as $row) {
            if (isset($row[$column1]) && isset($row[$column2])) {
                $result[] = [$column1 => $row[$column1], $column2 => $row[$column2]];
            }
        }
        return $result;
    }
    /**
     * @param string $callsign
     * @return User|Exception
     */
    public static function getUserByCallsign(string $callsign): User|Exception
    {
        static $result = [];
        if (isset($result[$callsign])) {
            return $result[$callsign];
        }
        $result[$callsign] = User::where('call', '=', $callsign)->firstOrFail();
        return $result[$callsign];
    }

    public static function getUserDataByCallsign(string $callsign, $testGsq = null): Array|Exception
    {
        $user = static::getUserByCallsign($callsign);
        $latlon = Log::convertGsqToDegrees($user['gsq']);
        $user['lat'] = $latlon['lat'];
        $user['lon'] = $latlon['lon'];
        $qths = [];
        if ($testGsq !== null) {
            $qths['Test Location'] = $coords = Log::convertGsqToDegrees($testGsq);
        }
        $qths += Log::getQthsForUser($user);
        $user['pota'] = array_filter(array_keys($qths), function($lbl) {
            return str_contains($lbl, 'POTA:');
        }) ? true : false;
        $coords = self::array_column_pair($qths,'lat', 'lon');
        $bounds = self::calculateEnclosingCircle($coords);
        //dump($bounds);
        // 44.16351145111103 -79.67774031160585 0.7622065421564141
        // $bounds = ['center' => [44.16351145111103, -79.67774031160585], 'radius' =>0.7622065421564141 ];
        //dd($bounds);
        return [
            'bands' =>      Log::getBandsForUser($user),
            'modes' =>      Log::getModesForUser($user),
            'gsqs' =>       Log::getGsqsForUser($user),
            'qths' =>       $qths,
            'qth_names' =>  $user['qth_names'],
            'qth_bounds' => $bounds,
            'user' =>       $user
        ];
    }

    public function getLastLog(): string
    {
        if ($this['last_log'] === null) {
            return 'None';
        }
        if (Carbon::parse($this['last_log'])->diffInDays() >= self::RECENTDAYS) {
            return substr($this['last_log'], 0, 16);
        }
        $result = Carbon::parse($this['last_log'])->diffForHumans();
        return str_replace(
            ['second', 'minute'],
            ['sec', 'min'],
            $result
        );
    }

    public function getLastClublogPull(): string
    {
        if ($this['clublog_last_data_pull'] === null) {
            return 'Never' . ($this['clublog_last_result'] ? ' - ' . $this['clublog_last_result'] : '');
        }
        if (Carbon::parse($this['clublog_last_data_pull'])->diffInDays() >= self::RECENTDAYS) {
            return substr($this['clublog_last_data_pull'], 0, 10);
        }
        $result = Carbon::parse($this['clublog_last_data_pull'])->diffForHumans();
        return str_replace(
            ['second', 'minute'],
            ['sec', 'min'],
            $result
        );
    }

    public function getLastQrzPull(): string
    {
        if ($this['qrz_last_data_pull'] === null) {
            return 'Never' . ($this['qrz_last_result'] ? ' - ' . $this['qrz_last_result'] : '');
        }
        if (Carbon::parse($this['qrz_last_data_pull'])->diffInDays() >= self::RECENTDAYS) {
            return substr($this['qrz_last_data_pull'], 0, 10);
        }
        $result = Carbon::parse($this['qrz_last_data_pull'])->diffForHumans();
        return str_replace(
            ['second', 'minute'],
            ['sec', 'min'],
            $result
        );
    }

    public static function getQthNamesForUser(User $user): array
    {
        $qthNames = [];
        if ($user['qth_names']) {
            $qthNamesArr =     explode("\r\n", $user['qth_names']);
            foreach ($qthNamesArr as $qthName) {
                $bits = explode('=', $qthName);
                if (isset($bits[1])) {
                    $qthNames[strtoupper(trim($bits[0]))] = trim($bits[1]);
                }
            }
        }
        return $qthNames;
    }

    public static function getHideGsqsForUser(User $user): array
    {
        $gsqs = [];
        $locations = User::getQthNamesForUser($user);
        foreach ($locations as $gsq => $name) {
            if (strtoupper($name) === 'HIDE') {
                $gsqs[] = $gsq;
            }
        }
        return $gsqs;
    }


    /**
     * @param User $user
     * @return void
     */
    public static function updateStats(User $user) {
        $first = Log::where('userId','=',$user->id)->orderBy('date', 'asc')->orderBy('time', 'asc')->first();
        $last = Log::where('userId','=',$user->id)->orderBy('date', 'desc')->orderBy('time', 'desc')->first();
        $log_days = Log::selectRaw('COUNT(DISTINCT date) as log_days')
            ->where('userId', $user->id)
            ->whereNotIn('myGsq', self::getHideGsqsForUser($user))
            ->get()
            ->value('log_days');
        $logCount = Log::where('userId', '=', $user->id)->count();
        $qthCount = Log::where('userId', '=', $user->id)->count(DB::raw('DISTINCT myQth'));
        $user->setAttribute('qrz_last_result', 'OK');
        $user->setAttribute('first_log', $first->date . ' ' . $first->time);
        $user->setAttribute('last_log', $last->date . ' ' . $last->time);
        $user->setAttribute('log_days', $log_days);
        $user->setAttribute('log_count', $logCount);
        $user->setAttribute('qth_count', $qthCount);
        $qthsFromLogs = Log::getLogQthsForUser($user);
        $qthNamesForUser = User::getQthNamesForUser($user);
        $qthsCombined = $qthsFromLogs + $qthNamesForUser;
        $qthNames = [];
        foreach ($qthsCombined as $gsq => $qth) {
            $qthNames[] = $gsq . ' = ' . $qth;
        }
//        dd([$qthsFromLogs, $qthNamesForUser, $qthsCombined, $qthNames]);
        $user->setAttribute('qth_names', implode("\r\n", $qthNames));
        $user->save();
    }
}
