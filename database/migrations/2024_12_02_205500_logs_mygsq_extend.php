<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Doing this in eloquent causes error:
        // "Changing columns for table "logs" requires Doctrine DBAL. Please install the doctrine/dbal package."
        DB::statement("ALTER TABLE logs MODIFY COLUMN myGsq VARCHAR(10)");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE logs MODIFY COLUMN myGsq VARCHAR(8)");
    }
};
