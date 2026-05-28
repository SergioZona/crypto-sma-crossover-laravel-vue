<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculation_history', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->string('interval');
            $table->string('from_date');
            $table->string('to_date');
            $table->integer('short_period');
            $table->integer('long_period');
            $table->integer('crossover_count');
            $table->json('crossovers');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculation_history');
    }
};
