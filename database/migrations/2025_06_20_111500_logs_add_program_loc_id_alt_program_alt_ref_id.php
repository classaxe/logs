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
            $table->text('program')->after('myQth')->nullable();
            $table->text('locId')->after('program')->nullable();
            $table->text('altProgram')->after('locId')->nullable();
            $table->text('altLocId')->after('altProgram')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn('altLocId');
            $table->dropColumn('altProgram');
            $table->dropColumn('locId');
            $table->dropColumn('program');
        });
    }
};
