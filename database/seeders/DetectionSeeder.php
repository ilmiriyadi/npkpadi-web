<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Detection;
use App\Models\Land;

class DetectionSeeder extends Seeder
{
    public function run(): void
    {
        $land = Land::latest()->first(); // Ambil lahan yang paling baru dibuat

        if (!$land) {
            $this->command->warn('Skip Seeder: Belum ada lahan di database.');
            return;
        }

        $detections = [
            ['id' => 1, 'score' => 94.2, 'days' => 1], // Nitrogen
            ['id' => 2, 'score' => 91.5, 'days' => 0], // Fosfor
            ['id' => 3, 'score' => 88.5, 'days' => 0], // Kalium
            ['id' => 2, 'score' => 98.5, 'days' => 0], // Fosfor lagi
        ];

        foreach ($detections as $d) {
            Detection::create([
                'land_id' => $land->land_id,
                'nutrient_deficiency_id' => $d['id'],
                'image_path' => 'detections/test_daun.jpg',
                'confidence_score' => $d['score'],
                'is_synced' => true,
                'created_at' => now()->subDays($d['days']),
            ]);
        }
    }
}