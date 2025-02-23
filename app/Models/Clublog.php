<?php

namespace App\Models;

use Adif\adif;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Clublog extends Model
{
    const URL = 'https://clublog.org/getadif.php';

    public static function fetchLogs(User $user) {
        if (!$user->clublog_email || !$user->clublog_password || !$user->clublog_call) {
            return false;
        }
        if (!$user->first_log || !$user->last_log) {
            return false;
        }
        $url = static::URL;
        $data = [
            'email' =>      $user->clublog_email,
            'password' =>   $user->clublog_password,
            'call' =>       $user->clublog_call,
            'startyear' =>  substr($user->first_log, 0, 4),
            'startmonth' => (int)substr($user->first_log, 5, 2),
            'startday' =>   (int)substr($user->first_log, 8, 2),
            'endyear' =>    substr($user->last_log, 0, 4),
            'endmonth' =>   (int)substr($user->last_log, 5, 2),
            'endday' =>     (int)substr($user->last_log, 8, 2),
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // For HTTPS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        $user->setAttribute('clublog_last_data_pull', time());

        if (!$data) {
            $user->setAttribute('clublog_last_result','No clublog data for user');
            $user->save();
            return false;
        }
        if ("Invalid login" === $data) {
            $user->setAttribute('clublog_last_result','Invalid login credentials for clublog');
            $user->save();
            return false;
        }
        $adif = new adif($data);
        $records = $adif->parser();
        $user->setAttribute('clublog_last_result',count($records) . " downloaded");
        $user->save();
        return $records;
    }

    public static function updateClublogs(User $user) {
        $clublogs = Clublog::fetchLogs($user);
        DB::beginTransaction();
        clublog::where('userId', $user->id)->delete();
        foreach($clublogs as $log) {
            $date = substr($log['QSO_DATE'], 0, 4) . '-'
                . substr($log['QSO_DATE'], 4, 2) . '-'
                . substr($log['QSO_DATE'], 6, 2);
            $time = substr($log['TIME_ON'],0,2) . ':' . substr($log['TIME_ON'], 2, 2);
            clublog::insert([
                'userId' =>         $user->id,
                'date' =>           $date,
                'time' =>           $time,
                'call' =>           $log['CALL'],
                'band' =>           $log['BAND'],
                'qsl_received' =>   $log['QSL_RCVD'],
            ]);
        }
        DB::commit();
        return clublog::where('userId', $user->id)->count();
    }

    public static function purgeDupes() {
        // Fetch all clublog duplicate records for logs
        $dupes = DB::select("
            SELECT
                GROUP_CONCAT(
                    concat(`cl`.`id`, '|', `cl`.`qsl_received`)
                    order by
                        `cl`.`qsl_received`='Y' DESC,
                        `cl`.`id`
                ) AS `matches`
            FROM
                logs `l`
            INNER JOIN `clublogs` `cl` ON
                `l`.`userId` = `cl`.`userId`
                AND `l`.`date` = `cl`.`date`
                AND `l`.`time` = `cl`.`time`
                AND `l`.`call` = `cl`.`call`
                AND `l`.`band` = `cl`.`band`
            GROUP BY
                `l`.`userId`,
                `l`.`date`,
                `l`.`time`,
                `l`.`call`,
                `l`.`band`
            HAVING
                COUNT(*) > 1 AND
                GROUP_CONCAT(`cl`.`qsl_received`) IN ('Y,N', 'N,Y')"
        );
        $purgeIds = [];
        foreach ($dupes as $dupe) {
            $conf = false;
            $entries = explode(',', $dupe->matches);
            foreach ($entries as $entry) {
                $bits = explode('|', $entry);
                if (!$conf && $bits[1] === 'Y') {
                    $conf = true;
                } else {
                    $purgeIds[] = $bits[0];
                }
            }
        }
        foreach ($purgeIds as $id) {
            DB::statement('DELETE FROM clublogs where ID=' . $id);
        }
    }

    public static function updateLogs() {
        DB::statement("
            UPDATE
                `logs` `l`
            INNER JOIN `clublogs` `cl` ON
                `l`.`userId` = `cl`.`userId`
                AND `l`.`date` = `cl`.`date`
                AND `l`.`time` = `cl`.`time`
                AND `l`.`call` = `cl`.`call`
                AND `l`.`band` = `cl`.`band`
            SET
                `l`.`clublog_conf` = `cl`.`qsl_received`"
        );
    }
}
