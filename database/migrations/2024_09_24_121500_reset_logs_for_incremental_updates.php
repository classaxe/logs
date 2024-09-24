<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DELETE FROM logs LIMIT 10000000');
        DB::statement('ALTER TABLE `logs` AUTO_INCREMENT = 1');
        DB::statement('UPDATE users SET log_count=0, first_log=null, last_log=null, qrz_last_data_pull=null, qrz_last_result=null, qth_count=0 limit 10');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        print "Not possible to reverse the migration\n";
    }
};
