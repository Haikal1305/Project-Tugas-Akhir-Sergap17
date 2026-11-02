<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Seed roles & assign permissions.
     */
    public function run(): void
    {
        // Bersihkan cache permission Spatie sebelum assign
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ==============================
        // 1. Role: super-admin
        // ==============================
        // Role ini akan di-bypass otomatis lewat Gate::before()
        // di AppServiceProvider, jadi tidak perlu di-assign permission
        // satu per satu — otomatis punya akses ke SEMUA fitur.
        Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        // ==============================
        // 2. Role: admin (opsional, granular)
        // ==============================
        // Admin biasa yang permission-nya diatur manual (bukan bypass),
        // untuk sekarang diberi semua permission yang ada.
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $admin->syncPermissions(Permission::all());

        // ==============================
        // 3. Role: petugas (role terbatas)
        // ==============================
        // Petugas belum punya akses ke manajemen user/RBAC sama sekali,
        // karena modul operasionalnya belum ada. Nanti setelah modul
        // seperti patroli/insiden ditambahkan, permission-nya menyusul.
        Role::firstOrCreate([
            'name' => 'petugas',
            'guard_name' => 'web',
        ]);
    }
}