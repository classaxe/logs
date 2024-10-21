<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
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
        'log_count'
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
    ];

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

    public static function getUserDataByCallsign(string $callsign): Array|Exception
    {
        $user = static::getUserByCallsign($callsign);
        $latlon = Log::convertGsqToDegrees($user['gsq']);
        $user['lat'] = $latlon['lat'];
        $user['lon'] = $latlon['lon'];
        return [
            'bands' =>      Log::getBandsForUser($user),
            'modes' =>      Log::getModesForUser($user),
            'gsqs' =>       Log::getGsqsForUser($user),
            'qths' =>       Log::getQthsForUser($user),
            'qth_names' =>  $user['qth_names'],
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

    public function getLastQrzPull(): string
    {
        if ($this['qrz_last_data_pull'] === null) {
            return 'Never' . ($this['qrz_last_result'] ? ' - ' . $this['qrz_last_result'] : '');
        }
        if (Carbon::parse($this['qrz_last_data_pull'])->diffInDays() >= self::RECENTDAYS) {
            return substr($this['qrz_last_data_pull'], 0, 16);
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
        $user->setAttribute('qrz_last_data_pull', time());
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
