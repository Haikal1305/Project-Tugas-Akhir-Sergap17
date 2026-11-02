<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Manajemen User & Role...
            'user view',
            'user create',
            'user update',
            'user update role',
            'user update password',
            'user delete',
            'role view',
            'role create',
            'role update',
            'role delete',
            'role update permission',

            // Manajemen Attendance
            'attendance view',
            'attendance view all',
            'attendance create',
            'attendance delete',

            // Manajemen Location
            'location view',
            'location create',
            'location update',
            'location delete',
            'location assign',
            'location manage-all',

            // Manajemen Leave Request (Izin)
            'leave-request view',       // Melihat daftar izin sendiri
            'leave-request view all',   // Melihat daftar izin semua personel (Admin)
            'leave-request create',     // Membuat pengajuan izin
            'leave-request review',     // Approve / Reject izin (Admin/Supervisor)
            'leave-request delete',     // Menghapus data izin

            // ==============================
            // Manajemen Patrol Report (Laporan Patroli)
            // ==============================
            'patrol-report view',        // Gerbang dasar akses fitur (bisa buka halaman daftar laporan).
                                          // Cakupan LOKASI mana yang terlihat ditentukan ReBAC (guard 'basic',
                                          // dikelola lewat halaman Hak Kelola Pos) + laporan milik sendiri —
                                          // bukan lagi dari permission Spatie.
            'patrol-report create',      // Mengirim laporan patroli (Satpam)
            'patrol-report delete',      // Menghapus laporan patroli
            'patrol-report manage-all',  // Bypass ReBAC per-lokasi: boleh lihat SEMUA pos tanpa diatur
                                          // per-lokasi satu-satu (khusus admin pusat)

            // DIHAPUS: 'patrol-report view all'
            // Dulu jadi "gerbang global" tambahan sebelum ReBAC per-lokasi dicek — sekarang
            // fungsinya duplikat sepenuhnya dengan ReBAC (guard 'basic') + 'patrol-report
            // manage-all'. Kalau permission ini masih ter-assign ke role tertentu di database,
            // hapus assignment-nya juga (lihat catatan migrasi di bawah run()).

            // ==============================
            // Pelaporan (Export PDF/Excel Laporan Patroli)
            // Terpisah dari 'patrol-report view' — punya izin lihat laporan
            // TIDAK otomatis berarti punya izin export dokumen.
            // ==============================
            'patrol-report export',      // Export laporan patroli ke Excel/PDF (route group 'pelaporan.*')
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Bersihkan permission lama yang sudah tidak dipakai lagi di kode,
        // supaya tidak nyangkut di database & bikin bingung saat lihat daftar
        // role/permission di UI. Ini aman dijalankan berulang kali.
        Permission::where('name', 'patrol-report view all')
            ->where('guard_name', 'web')
            ->delete();
    }
}