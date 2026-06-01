<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom planting_date ke tabel lands yang sudah ada.
     */
    public function up(){
        // Cek dulu apakah kolom planting_date SUDAH ADA atau BELUM
        if (!Schema::hasColumn('lands', 'planting_date')) {
            Schema::table('lands', function (Blueprint $table) {
                $table->date('planting_date')->nullable()->after('location');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lands', function (Blueprint $table) {
            $table->dropColumn('planting_date');
        });
    }
};
