<?php

use Illuminate\Database\Migrations\Migration;
use \App\Models\ParkMatch;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $file = resource_path('csv/na-23jun2025-csv.csv');
        $prefixes = ['CA','GB','MX','PL','PM','US'];
        ParkMatch::ingestDualparksCsv($file, $prefixes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $file = resource_path('csv/na-23jun2025-csv.csv');
        $prefixes = ['CA','MX','PL','PM','US'];
        ParkMatch::ingestDualparksCsv($file, $prefixes);
    }
};
