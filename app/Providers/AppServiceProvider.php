<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Lauthz\Facades\Enforcer;
use Illuminate\Support\Facades\DB;
use App\Models\PatrolReport;
use App\Observers\PatrolReportObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // FIX BUG PACKAGE casbin/laravel-authz v5.0.0:
        // LauthzServiceProvider mem-bind Lauthz\Loaders\ModelLoaderManager sebagai
        // SINGLETON. ModelLoaderManager extends Illuminate\Support\Manager, yang
        // meng-cache driver berdasarkan NAMA TIPE ('text', 'file', 'url') — bukan
        // per-guard. Karena guard 'basic' dan 'attendance' sama-sama pakai
        // config_type => 'text', siapa pun guard yang di-resolve LEBIH DULU akan
        // "mengunci" TextLoader dengan config model miliknya sendiri, dan guard
        // lain yang di-resolve setelahnya ikut memakai config yang salah itu.
        // Ini yang menyebabkan model 'g'/[role_definition] guard basic hilang,
        // atau request_definition attendance ketuker jadi punya basic (expected 3
        // got 4), tergantung urutan resolve.
        //
        // Fix: override binding-nya di sini supaya TIDAK singleton, sehingga tiap
        // guard yang di-resolve EnforcerManager selalu dapat ModelLoaderManager
        // baru (dan cache driver-nya ikut baru), tidak ada state yang ke-share
        // antar guard. Ini harus dijalankan di register(), sebelum service
        // provider lain (termasuk LauthzServiceProvider) sempat resolve binding
        // ini duluan.
        $this->app->bind(\Lauthz\Loaders\ModelLoaderManager::class, function ($app) {
            return new \Lauthz\Loaders\ModelLoaderManager($app);
        });
    }

    public function boot(): void
    {
        // 1. Spatie / Gate Bypass otomatis untuk super-admin
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        // 2. Daftarkan Observer notifikasi (PatrolReport)
        // Ditaruh di sini (sebelum blok Casbin di bawah) supaya registrasi
        // Observer tetap jalan meskipun ada masalah di blok ABAC.
        PatrolReport::observe(PatrolReportObserver::class);

        // Guard 'attendance' butuh tabel attendance_rules untuk resolve policy-nya.
        // Skip registrasi function ini kalau tabel belum ada (misal saat migrate
        // pertama kali dijalankan / setelah rollback / migrate:fresh), supaya
        // artisan command lain (termasuk migrate itu sendiri) tidak ikut gagal.
        if (\Illuminate\Support\Facades\Schema::hasTable('attendance_rules')) {
            // 2. ABAC MURNI: Fungsi untuk guard 'attendance'
            Enforcer::guard('attendance')->addFunction('isAssigned', function ($userId, $locationId) {
                return DB::table('location_user')
                    ->where('user_id', $userId)
                    ->where('location_id', $locationId)
                    ->exists();
            });

            Enforcer::guard('attendance')->addFunction('checkRadius', function ($envLat, $envLng, $objLat, $objLng, $radius) {
                if ($objLat === null || $objLng === null || $radius === null) {
                    return false;
                }

                $envLat = (float) $envLat;
                $envLng = (float) $envLng;
                $objLat = (float) $objLat;
                $objLng = (float) $objLng;
                $radius = (float) $radius;

                $earthRadius = 6371000;

                $dLat = deg2rad($objLat - $envLat);
                $dLng = deg2rad($objLng - $envLng);

                $a = sin($dLat / 2) ** 2
                    + cos(deg2rad($envLat)) * cos(deg2rad($objLat)) * sin($dLng / 2) ** 2;
                
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadius * $c;

                return $distance <= $radius;
            });
        }
    }
}