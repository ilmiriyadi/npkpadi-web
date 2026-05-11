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
        $this->call([
            UserSeeder::class,           // Akun Admin dibuat pertama
            NutrientDeficiencySeeder::class, // Data Master Penyakit
            // DetectionSeeder::class,    // <--- COMMENT INI kalau mau kosong
        ]);
    }
}