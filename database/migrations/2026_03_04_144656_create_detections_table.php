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
        Schema::create('detections', function (Blueprint $table) {
            $table->id('detection_id');
            $table->foreignId('land_id')->references('land_id')->on('lands')->onDelete('cascade');
            $table->foreignId('nutrient_deficiency_id')->references('nutrient_deficiency_id')->on('nutrient_deficiencies')->onDelete('cascade');
            $table->string('image_path', 255);
            $table->float('confidence_score'); // Nilai akurasi AI
            $table->boolean('is_synced')->default(false); // Penanda sinkronisasi data lokal ke cloud
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detections');
    }
};
