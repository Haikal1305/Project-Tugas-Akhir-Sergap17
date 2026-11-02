<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Seeder;
use Lauthz\Facades\Enforcer;

class CasbinPolicySeeder extends Seeder
{
    public function run(): void
    {
        Log::info('=== CasbinPolicySeeder: START ===');

        // ==================================================
        // 1. Policy ReBAC untuk guard 'basic'
        // (Location: view/update/delete/assign/export)
        // p(act, act) adalah "identity mapping" yang dibutuhkan matcher basic guard:
        // m = g(r.sub, p.role, r.obj) && r.act == p.act
        // -> Casbin butuh satu baris p per action supaya bisa mencocokkan g(sub, action, obj)
        // ==================================================
        try {
            $basicActions = ['view', 'update', 'delete', 'assign', 'export'];

            foreach ($basicActions as $act) {
                $has = Enforcer::guard('basic')->hasPolicy($act, $act);
                Log::info("CasbinPolicySeeder [basic]: hasPolicy('{$act}', '{$act}') = " . var_export($has, true));

                if (!$has) {
                    $added = Enforcer::guard('basic')->addPolicy($act, $act);
                    Log::info("CasbinPolicySeeder [basic]: addPolicy('{$act}', '{$act}') result = " . var_export($added, true));
                }
            }
        } catch (\Throwable $e) {
            Log::error('CasbinPolicySeeder [basic]: EXCEPTION -> ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }

        // ==================================================
        // 2. Guard 'patrol' — SUDAH TIDAK DIPAKAI. Diabaikan.
        // ==================================================

        // ==================================================
        // 3. Policy ABAC untuk guard 'attendance' (Operasional Satpam)
        // ==================================================
        $attendanceSubRule = 'isAssigned(r.sub.id, r.obj.id)';
        $attendanceEnvRule = 'checkRadius(r.env.lat, r.env.lng, r.obj.lat, r.obj.lng, r.obj.radius)';

        foreach (['clock_in', 'clock_out'] as $act) {
            try {
                $has = Enforcer::guard('attendance')->hasPolicy($attendanceSubRule, $attendanceEnvRule, $act);
                Log::info("CasbinPolicySeeder [attendance]: hasPolicy(..., '{$act}') = " . var_export($has, true));

                if (!$has) {
                    $added = Enforcer::guard('attendance')->addPolicy($attendanceSubRule, $attendanceEnvRule, $act);
                    Log::info("CasbinPolicySeeder [attendance]: addPolicy(..., '{$act}') result = " . var_export($added, true));
                } else {
                    Log::info("CasbinPolicySeeder [attendance]: policy '{$act}' SKIPPED karena hasPolicy() sudah true.");
                }
            } catch (\Throwable $e) {
                Log::error("CasbinPolicySeeder [attendance]: EXCEPTION on '{$act}' -> " . $e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }

        // Baru: laporan patroli hanya bisa dikirim kalau ditugaskan, sedang on-duty
        // (sudah clock-in, belum clock-out), dan masih dalam jendela jam shift.
        $patrolSubRule = 'r.sub.assigned == true && r.sub.on_duty == true';
        $patrolEnvRule = "r.obj.shift_start == '' || (r.env.now >= r.obj.shift_start && r.env.now <= r.obj.shift_end)";

        try {
            $has = Enforcer::guard('attendance')->hasPolicy($patrolSubRule, $patrolEnvRule, 'patrol_create');
            Log::info("CasbinPolicySeeder [attendance]: hasPolicy(..., 'patrol_create') = " . var_export($has, true));

            if (!$has) {
                $added = Enforcer::guard('attendance')->addPolicy($patrolSubRule, $patrolEnvRule, 'patrol_create');
                Log::info("CasbinPolicySeeder [attendance]: addPolicy(..., 'patrol_create') result = " . var_export($added, true));
            } else {
                Log::info("CasbinPolicySeeder [attendance]: policy 'patrol_create' SKIPPED karena hasPolicy() sudah true.");
            }
        } catch (\Throwable $e) {
            Log::error("CasbinPolicySeeder [attendance]: EXCEPTION on 'patrol_create' -> " . $e->getMessage());
            Log::error($e->getTraceAsString());
        }

        // ==================================================
        // Verifikasi akhir: hitung langsung dari DB, bukan dari cache Enforcer
        // ==================================================
        $countBasic = \DB::table('rules')->count();
        $countAttendance = \DB::table('attendance_rules')->count();

        Log::info("CasbinPolicySeeder: FINAL COUNT -> rules(basic) = {$countBasic}, attendance_rules = {$countAttendance}");
        Log::info('=== CasbinPolicySeeder: END ===');
    }
}