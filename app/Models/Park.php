<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Park extends Model
{
    const UA = 'Log-Viewer v%s | (C) %s VA3PHP';
    const URL_POTA = 'https://api.pota.app/program/parks/';
    const URL_WWFF = 'https://wwff.co/wwff-data/wwff_directory.csv';

    const ISO3166_WWFF = [
        'CA' => 'VEFF',
        'MX' => 'XEFF',
        'PL' => 'SPFF',
        'PM' => 'FFF', // Includes loads of islands beside Saint Pierre and Miquelon
        'US' => 'KFF'
    ];

    public static function convertIsoToDxccPrefixes($prefixes) {
        $dxcc = [];
        foreach ($prefixes as $prefix) {
            if (isset(static::ISO3166_WWFF[$prefix])) {
                $dxcc[] = static::ISO3166_WWFF[$prefix];
            }
        }
        sort($dxcc);
        return $dxcc;
    }

    public static function fetchPotaParks($prefix) {
        $url = static::URL_POTA . $prefix;
        try {
            $raw = file_get_contents($url);
        } catch (\Exception $e) {
            print $e->getMessage();
            return false;
        }
        return json_decode($raw);
    }

    public static function fetchWwffParks() {
        $url = static::URL_WWFF;
        $version = exec('git describe --tags');
        $context = stream_context_create([
            'http'=> [
                'method'=>"GET",
                'header'=>"User-Agent: " . sprintf(static::UA, $version, date('Y')) . "\r\n"
            ]
        ]);
        $raw = file_get_contents($url, false, $context);
        $fp = fopen("php://temp", 'r+');
        fputs($fp, $raw);
        rewind($fp);
        $data = [];
        try {
            $header = fgetcsv($fp);
            $fields = count($header);
            while (($row = fgetcsv($fp)) !== false ) {
                if (count($row) === $fields) {
                    $data[] = (object) array_combine($header, $row);
                }
            }
            fclose($fp);
        } catch (\Exception $e) {
            print $e->getMessage();
            return false;
        }
        return $data;
    }

    public static function formatProgramPrefixCount($program, $prefix) {
        $count = Park::where([
            ['prefix', $prefix],
            ['program', $program]
        ])->count();
        return $program . ' '
            . str_pad($prefix . ':', 5, ' ') . ' '
            . str_pad($count, 5, ' ', STR_PAD_LEFT)
            . " parks\n";
    }

    public static function getParks($lat0, $lng0, $lat1, $lng1) {
        return DB::select(DB::raw("SELECT
                * FROM (
                    SELECT 'BOTH' AS `program`,
                        p1.reference AS `pota`,
                        p2.reference AS `wwff`,
                        p1.name,
                        p1.lat,
                        p1.lng,
                        p1.gsq
                    FROM
                        parks p1
                    INNER JOIN
                        park_matches pm ON p1.reference = pm.ref1
                    INNER JOIN
                        parks p2 ON p2.reference = pm.ref2

                    UNION SELECT
                        `program`,
                        `reference`,
                        null,
                        `name`,
                        `lat`,
                        `lng`,
                        `gsq`
                    FROM
                        `parks`
                    WHERE
                        `program` = 'POTA' AND
                        `reference` NOT IN(SELECT ref1 from park_matches)

                    UNION SELECT
                        `program`,
                        null,
                        `reference`,
                        `name`,
                        `lat`,
                        `lng`,
                        `gsq`
                    FROM
                        `parks`
                    WHERE
                        `program` = 'WWFF' AND
                        `reference` NOT IN(SELECT ref2 from park_matches)
                ) PX
            WHERE
                lat <= $lat0 AND
                lng <= $lng0 AND
                lat >= $lat1 AND
                lng >= $lng1
        "));
    }

    public static function updateDualParks() {
        // For when we have an endpoint for this
    }

    public static function updatePotaParks($prefixes) {
        $program = 'POTA';
        foreach($prefixes as $prefix) {
            $parks = Park::fetchPotaParks($prefix);
            if (!$parks) {
                continue;
            }
            DB::beginTransaction();
            Park::where([
                ['prefix', $prefix],
                ['program', $program]
            ])->delete();

            foreach($parks as $park) {
                Park::insert([
                    'program' =>    $program,
                    'reference' =>  $park->reference,
                    'name' =>       $park->name,
                    'prefix' =>     explode('-', $park->reference)[0],
                    'number' =>     explode('-', $park->reference)[1],
                    'lat' =>        $park->latitude,
                    'lng' =>        $park->longitude,
                    'gsq' =>        $park->grid,
                    'location' =>   $park->locationDesc
                ]);
            }
            DB::commit();
            $count = Park::where([
                ['prefix', $prefix],
                ['program', $program]
            ])->count();
            print static::formatProgramPrefixCount($program, $prefix);
        }
    }

    public static function updateWwffParks($prefixes) {
        $program = 'WWFF';
        $dxcc = self::convertIsoToDxccPrefixes($prefixes);
        $parks = Park::fetchWwffParks();
        if (!$parks) {
            return;
        }
        DB::beginTransaction();
            foreach($dxcc as $prefix) {
                Park::where([
                    ['prefix', $prefix],
                    ['program', $program]
                ])->delete();
            }
            foreach($parks as $park) {
                if (!in_array($park->program, $dxcc)) {
                    continue;
                }
                if ($park->status !== 'active') {
                    continue;
                }
                Park::insert([
                    'program' =>    $program,
                    'reference' =>  $park->reference,
                    'name' =>       $park->name,
                    'prefix' =>     explode('-', $park->reference)[0],
                    'number' =>     explode('-', $park->reference)[1],
                    'lat' =>        $park->latitude,
                    'lng' =>        $park->longitude,
                    'gsq' =>        $park->iaruLocator,
                    'location' =>   $park->notes
                ]);
            }
            foreach($dxcc as $prefix) {
                print static::formatProgramPrefixCount($program, $prefix);
            }
        DB::commit();
    }

    public static function updateParks($prefixes) {
        self::updatePotaParks($prefixes);
        self::updateWwffParks($prefixes);
        // self::updateDualParks();
    }
}
