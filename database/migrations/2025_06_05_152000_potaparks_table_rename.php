<?php

use App\Models\Park;
use App\Models\User;
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
        DB::statement("RENAME TABLE `logs`.`potaparks` TO `logs`.`parks`");

        Schema::table('parks', function (Blueprint $table) {
            $table->string('program')->nullable();
            $table->index('program');
        });

        DB::statement("DELETE FROM `logs`.`parks` WHERE `program` IS NULL");
        $prefixes = User::getAllUserItus();
        $prefixes[] = 'MX';
        $prefixes[] = 'PM';
        foreach($prefixes as $prefix) {
            $count = Park::updateParks($prefix);
            print "\n  * Imported $prefix: $count parks";
        }
        print "\n  " . str_repeat('.', 41);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            $table->dropIndex(['program']);
            $table->dropColumn('program');
        });
        DB::statement("RENAME TABLE `logs`.`parks` TO `logs`.`potaparks`");
    }
};
