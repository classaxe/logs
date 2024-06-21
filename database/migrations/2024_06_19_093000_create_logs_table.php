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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userId')->nullable()->index();
            $table->string('qrzId');
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('call');
            $table->string('band');
            $table->string('mode')->nullable();
            $table->string('rx')->nullable();
            $table->string('tx')->nullable();
            $table->string('pwr')->nullable();
            $table->string('qth')->nullable();
            $table->string('sp')->nullable();
            $table->string('itu')->nullable();
            $table->string('continent')->nullable();
            $table->string('gsq')->nullable();
            $table->integer('km')->nullable();
            $table->string('conf')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
