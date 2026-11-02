<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's users.
     */
    public function run(): void
    {
        // ==============================
        // Akun Super Admin (bypass semua fitur)
        // ==============================
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'admin@sergap17.com',
            'password' => bcrypt('password'), // ganti sebelum production
        ]);

        $superAdmin->assignRole('super-admin');

        // ==============================
        // Akun Test User biasa (opsional, untuk testing)
        // ==============================
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);

        $testUser->assignRole('petugas');
    }
}