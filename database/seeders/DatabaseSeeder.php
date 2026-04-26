<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\NutrientDeficiency;
use App\Models\Land;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data awal untuk testing lokal.
     */
    public function run(): void
    {
        // ============================
        // 1. Akun Admin
        // ============================
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@npkpadi.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);
        $this->command->info("✔ Admin dibuat: admin@npkpadi.com / password123");

        // ============================
        // 2. Akun Petani (yang terhubung ke Pi)
        // ============================
        $petani = User::create([
            'name'     => 'Petani Demo',
            'email'    => 'petani@npkpadi.com',
            'password' => Hash::make('password123'),
            'role'     => 'farmer',
        ]);
        $this->command->info("✔ Petani dibuat: petani@npkpadi.com / password123 (user_id: {$petani->user_id})");

        // ============================
        // 3. Data Master Defisiensi Nutrisi
        // ============================
        NutrientDeficiency::create([
            'name'     => 'Nitrogen (N)',
            'solution' => 'Berikan pupuk Urea atau ZA sesuai dosis anjuran. Dosis: 200-300 kg Urea/ha.',
        ]);
        NutrientDeficiency::create([
            'name'     => 'Fosfor (P)',
            'solution' => 'Berikan pupuk SP-36 atau TSP sesuai dosis anjuran. Dosis: 100-150 kg SP-36/ha.',
        ]);
        NutrientDeficiency::create([
            'name'     => 'Kalium (K)',
            'solution' => 'Berikan pupuk KCl atau K₂SO₄ sesuai dosis anjuran. Dosis: 50-100 kg KCl/ha.',
        ]);
        $this->command->info("✔ Data master defisiensi nutrisi: N, P, K");

        // ============================
        // 4. Lahan Default untuk Petani
        // ============================
        Land::create([
            'user_id'  => $petani->user_id,
            'name'     => 'Sawah Utara',
            'location' => 'Banjarmasin',
        ]);
        Land::create([
            'user_id'  => $petani->user_id,
            'name'     => 'Sawah Selatan',
            'location' => 'Banjarbaru',
        ]);
        $this->command->info("✔ 2 lahan dibuat untuk Petani Demo");

        $this->command->newLine();
        $this->command->info("======================================");
        $this->command->info("  PENTING: SYNC_PI_USER_ID = {$petani->user_id}");
        $this->command->info("  Pastikan ini sama di .env dan app.py");
        $this->command->info("======================================");
    }
}