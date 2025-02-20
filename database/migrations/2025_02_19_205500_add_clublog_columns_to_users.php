<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('clublog_email')->nullable();
            $table->string('clublog_password')->nullable();
            $table->string('clublog_call')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('clublog_email');
            $table->dropColumn('clublog_password');
            $table->dropColumn('clublog_call');
        });
    }
};
