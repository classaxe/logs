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
        Schema::create('clublogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->nullable()->index();
            $table->string('date')->nullable()->index();
            $table->string('time')->nullable()->index();
            $table->string('call')->index();
            $table->string('band')->index();
            $table->string('qsl_received');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clublogs');
    }
};
