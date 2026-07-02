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
        Schema::table('nutrient_deficiencies', function (Blueprint $table) {
            // Hapus kolom lama jika masih ada (dulu bernama solution, dll)
            if (Schema::hasColumn('nutrient_deficiencies', 'solution')) {
                $table->dropColumn(['solution', 'solution_vegetative', 'solution_generative', 'solution_ripening']);
            }

            // Tambahkan kolom baru
            if (!Schema::hasColumn('nutrient_deficiencies', 'saran_umum_unggul')) {
                $table->text('saran_umum_unggul')->nullable()->after('name');
            }
            if (!Schema::hasColumn('nutrient_deficiencies', 'saran_umum_lokal')) {
                $table->text('saran_umum_lokal')->nullable()->after('saran_umum_unggul');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nutrient_deficiencies', function (Blueprint $table) {
            $table->dropColumn(['saran_umum_unggul', 'saran_umum_lokal']);
        });
    }
};
