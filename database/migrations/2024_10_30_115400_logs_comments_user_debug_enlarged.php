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
        Schema::table('logs', function (Blueprint $table) {
            $table->text('comment')->after('call');
        });
        DB::statement("ALTER TABLE users MODIFY COLUMN qrz_last_data_pull_debug TEXT DEFAULT NULL");
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
        DB::statement("UPDATE users SET qrz_last_data_pull_debug=SUBSTRING(qrz_last_data_pull_debug, 0, 240)");
        DB::statement("ALTER TABLE users MODIFY COLUMN qrz_last_data_pull_debug VARCHAR(255) DEFAULT NULL");
    }
};
