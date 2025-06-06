<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Park extends Model
{
    const POTA_URL = 'https://api.pota.app/program/parks/';

    public static function fetchPotaParks($prefix) {
        $url = static::POTA_URL . $prefix;
        try {
            $raw = file_get_contents($url);
            $json = json_decode($raw);
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        return $json;
    }

    public static function getParks($lat0, $lng0, $lat1, $lng1) {
        return Park::where([
            [ 'lat', '<=', $lat0 ],
            [ 'lng', '>=', $lng0 ],
            [ 'lat', '>=', $lat1 ],
            [ 'lng', '<=', $lng1 ]
        ])
        ->get()
        ->toArray();
    }

    public static function updateParks($prefix) {
        $parks = Park::fetchPotaParks($prefix);
        DB::beginTransaction();
            Park::where([
                ['prefix', $prefix],
                ['program', 'POTA']
            ])->delete();

            foreach($parks as $park) {
                Park::insert([
                    'program' =>    'POTA',
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
        return Park::where('prefix', $prefix)->count();
    }
}
