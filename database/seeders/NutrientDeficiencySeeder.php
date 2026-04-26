<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NutrientDeficiency;

class NutrientDeficiencySeeder extends Seeder
{
    public function run(): void
    {
        // Hanya 3 kondisi: Nitrogen, Fosfor, dan Kalium
        $data = [
            ['id' => 1, 'name' => 'Defisiensi Nitrogen (N)', 'solution' => 'Berikan pupuk Urea atau ZA sesuai dosis anjuran.'],
            ['id' => 2, 'name' => 'Defisiensi Fosfor (P)', 'solution' => 'Gunakan pupuk SP-36 atau TSP untuk memperkuat akar dan batang.'],
            ['id' => 3, 'name' => 'Defisiensi Kalium (K)', 'solution' => 'Tambahkan pupuk KCL agar pengisian bulir padi lebih maksimal.'],
        ];

        foreach ($data as $item) {
            NutrientDeficiency::updateOrCreate(
                ['nutrient_deficiency_id' => $item['id']], 
                [
                    'name' => $item['name'], 
                    'solution' => $item['solution']
                ]
            );
        }

        $this->command->info('Data Master NPK (Tanpa Sehat) Berhasil Disinkronkan!');
    }
}