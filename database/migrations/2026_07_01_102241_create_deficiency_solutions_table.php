<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deficiency_solutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nutrient_deficiency_id')->references('nutrient_deficiency_id')->on('nutrient_deficiencies')->onDelete('cascade');
            $table->enum('seed_type', ['unggul', 'lokal']);
            $table->integer('min_hst');
            $table->integer('max_hst');
            $table->text('solution_detail');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deficiency_solutions');
    }
};