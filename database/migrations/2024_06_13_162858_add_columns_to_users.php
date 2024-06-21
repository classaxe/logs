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
            $table->string('gsq');
            $table->string('qth')->nullable();
            $table->string('city')->nullable();
            $table->string('sp')->nullable();
            $table->string('itu')->nullable();
            $table->string('call');
            $table->string('qrz_api_key');
            $table->timestamp('qrz_last_data_pull')->nullable();
            $table->integer('is_visible')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gsq');
            $table->dropColumn('qth');
            $table->dropColumn('city');
            $table->dropColumn('sp');
            $table->dropColumn('itu');
            $table->dropColumn('call');
            $table->dropColumn('qrz_api_key');
            $table->dropColumn('qrz_last_data_pull');
            $table->dropColumn('is_visible');
        });
    }
};
