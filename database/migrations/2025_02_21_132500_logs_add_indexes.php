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
        Schema::table('logs', function (Blueprint $table) {
            $table->index('date');
            $table->index('time');
            $table->index('call');
            $table->index('band');
            $table->index('conf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['time']);
            $table->dropIndex(['call']);
            $table->dropIndex(['band']);
            $table->dropIndex(['conf']);
        });
    }
};
