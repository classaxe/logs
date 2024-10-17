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
        Schema::table('users', function (Blueprint $table) {
            $table->text('log_days')->after('last_log');
        });
        $users = User::getActiveUsers();
        try {
            foreach ($users as $user){
                User::updateStats($user);
            }
        } catch (Exception $e) {
            dd($e->getMessage() . " for user " . $user['call']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('log_days');
        });
    }
};
