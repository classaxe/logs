<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ParkMatch extends Model
{
    use HasFactory;
    protected $table = 'park_matches';

    public static function ingestDualparksCsv($path, array $prefixes) {
        DB::statement('DELETE FROM park_matches LIMIT 10000000');

        $imports = [];
        $fh = fopen($path, 'r');
        $headers = fgetcsv($fh);
        while (($row = fgetcsv($fh, 1000, ',')) !== false) {
            $imports[] = array_combine($headers, $row);
        }
        fclose($fh);
        $data = [];
        foreach ($imports as $import) {
            if (empty($import['POTA:']) || empty($import['WWFF:'])) {
                continue;
            }
            if (!in_array(substr($import['POTA:'], 0, 2), $prefixes)) {
                continue;
            }
            $data[] = [
                'id' =>         null,
                'ref1' =>       $import['POTA:'],
                'ref2' =>       $import['WWFF:'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        ParkMatch::insert($data);
    }
}
