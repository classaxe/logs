<?php

use App\Models\Log;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE `users` SET `log_days`='0' WHERE `log_days` = ''");
        DB::statement("ALTER TABLE users MODIFY COLUMN log_days INT default 0, MODIFY COLUMN `qrz_last_data_pull_debug` VARCHAR(255) default null");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN log_days TEXT, MODIFY COLUMN `qrz_last_data_pull_debug` TEXT");
        DB::statement("UPDATE `users` SET `log_days`='' WHERE `log_days` = '0'");
    }
};
