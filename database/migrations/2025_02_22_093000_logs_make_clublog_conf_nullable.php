<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `logs`.`logs` CHANGE COLUMN `clublog_conf` `clublog_conf` CHAR(1) NULL DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `logs`.`logs` CHANGE COLUMN `clublog_conf` `clublog_conf` CHAR(1) NOT NULL");
    }
};
