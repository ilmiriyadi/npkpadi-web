<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NutrientDeficiency;

class NutrientDeficiencySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat 3 Data Penyakit Utama
        $diseases = [
            'Kekurangan Nitrogen (N)',
            'Kekurangan Fosfor (P)',
            'Kekurangan Kalium (K)'
        ];

        foreach($diseases as $disease) {
            // Gunakan updateOrCreate agar tidak duplikat jika di-seed berkali-kali
            NutrientDeficiency::updateOrCreate(
                ['name' => $disease],
                [
                    'saran_umum_unggul' => 'Saran umum untuk bibit unggul belum diisi. Silakan edit melalui panel admin.',
                    'saran_umum_lokal'  => 'Saran umum untuk bibit lokal belum diisi. Silakan edit melalui panel admin.'
                ]
            );
        }
    }
}