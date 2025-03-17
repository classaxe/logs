<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\County;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("UPDATE logs SET `sp`='AK' WHERE `itu`='Alaska'");
        DB::statement("UPDATE logs SET `sp`='HI' WHERE `itu`='Hawaii'");
        DB::statement("UPDATE logs SET `sp`='PR' WHERE `itu`='Puerto Rico'");
        DB::statement("UPDATE logs SET `sp`='VI' WHERE `itu`='US Virgin Islands'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Not reversible
    }
};
