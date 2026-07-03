<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Detection;
use App\Models\Land;

class DetectionSeeder extends Seeder
{
    public function run(): void
    {
        // Mengambil semua lahan agar deteksi tersebar di berbagai lahan
        $lands = Land::all(); 

        if ($lands->isEmpty()) {
            $this->command->warn('Skip Seeder: Belum ada lahan di database.');
            return;
        }

        foreach ($lands as $land) {
            // Membuat data deteksi dummy untuk setiap lahan
            Detection::create([
                'land_id' => $land->land_id,
                'nutrient_deficiency_id' => rand(1, 3), // ID penyakit 1-3
                'image_path' => 'detections/test_daun.jpg',
                'confidence_score' => rand(85, 99),
                'is_synced' => true,
                'created_at' => now()->subDays(rand(0, 30)), // Deteksi dalam 30 hari terakhir
            ]);
        }
    }
}