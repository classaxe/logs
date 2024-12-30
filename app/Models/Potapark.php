<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Potapark extends Model
{
    const URL = 'https://api.pota.app/program/parks/';

    public static function fetchParks($prefix) {
        $url = static::URL . $prefix;
        try {
            $raw = file_get_contents($url);
            $json = json_decode($raw);
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        return $json;
    }

    public static function updateParks($prefix) {
        $parks = Potapark::fetchParks($prefix);
        DB::beginTransaction();
        Potapark::where('prefix', $prefix)->delete();
        foreach($parks as $park) {
            Potapark::insert([
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
        return Potapark::where('prefix', $prefix)->count();
    }
}
