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
        DB::statement("ALTER TABLE logs MODIFY COLUMN program VARCHAR(5)");
        DB::statement("ALTER TABLE logs MODIFY COLUMN locId VARCHAR(10)");
        DB::statement("ALTER TABLE logs MODIFY COLUMN altProgram VARCHAR(5)");
        DB::statement("ALTER TABLE logs MODIFY COLUMN altLocId VARCHAR(10)");

        Schema::table('logs', function (Blueprint $table) {
            $table->index('program');
            $table->index('locId');
            $table->index('altProgram');
            $table->index('altLocId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropIndex('logs_altlocid_index');
            $table->dropIndex('logs_altprogram_index');
            $table->dropIndex('logs_locid_index');
            $table->dropIndex('logs_program_index');
        });

        DB::statement("ALTER TABLE logs MODIFY COLUMN program TEXT");
        DB::statement("ALTER TABLE logs MODIFY COLUMN locId TEXT");
        DB::statement("ALTER TABLE logs MODIFY COLUMN altProgram TEXT");
        DB::statement("ALTER TABLE logs MODIFY COLUMN altLocId TEXT");

    }
};
