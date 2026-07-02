<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\NutrientDeficiency;
use App\Models\Land;
use App\Models\Detection;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat 3 Data Penyakit Utama
        $diseases = [
            'Kekurangan Nitrogen (N)',
            'Kekurangan Fosfor (P)',
            'Kekurangan Kalium (K)'
        ];

        foreach($diseases as $disease) {
            NutrientDeficiency::create([
                'name' => $disease,
                'saran_umum_unggul' => 'Saran umum untuk bibit unggul belum diisi.',
                'saran_umum_lokal'  => 'Saran umum untuk bibit lokal belum diisi.'
            ]);
        }

        $this->call([
            UserSeeder::class,           // Akun Admin dibuat pertama
            NutrientDeficiencySeeder::class, // Data Master Penyakit
            // DetectionSeeder::class,    // <--- COMMENT INI kalau mau kosong
        ]);
    }
}