<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    const RECENTDAYS = 2;

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

    /**
     * @return array
     */
    public static function getActiveUsers(): Collection
    {
        return User::where('is_visible', 1)->orderBy('call', 'asc')->get();
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
        return [
            'bands' =>  Log::getBandsForUserId($user['id']),
            'modes' =>  Log::getModesForUserId($user['id']),
            'user' =>   $user
        ];
    }

    public function getLastLog(): string
    {
        if ($this['last_log'] === null) {
            return 'Never';
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

}
