<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@npkpadi.com'], // Cari berdasarkan email
            [
                'name' => 'Admin Pakar',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );
    }
}
