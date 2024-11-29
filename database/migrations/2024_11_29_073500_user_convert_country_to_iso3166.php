<?php
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE `users` SET `itu`='CA' WHERE `itu` = 'CAN'");
        DB::statement("UPDATE `users` SET `itu`='US' WHERE `itu` = 'USA'");
    }

    public function down(): void
    {
        DB::statement("UPDATE `users` SET `itu`='CAN' WHERE `itu` = 'CA'");
        DB::statement("UPDATE `users` SET `itu`='USA' WHERE `itu` = 'US'");
    }
};
