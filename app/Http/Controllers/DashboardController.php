<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\PatrolReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Lauthz\Facades\Enforcer;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        // =========================================================
        // 1 & 2. Patroli Aktif & Insiden Berjalan
        //    Scoping PERSIS mengikuti pola PatrolReportController::index():
        //    - 'patrol-report manage-all' = bypass total (admin pusat)
        //    - selain itu: laporan milik sendiri + laporan dari lokasi yang
        //      di-grant action 'view' via ReBAC (guard 'basic')
        //    Status yang tersedia di kolom PatrolReport: aman, mencurigakan,
        //    temuan_darurat (lihat PatrolReportController::store()).
        //    "Insiden Berjalan" = laporan hari ini berstatus mencurigakan
        //    atau temuan_darurat.
        // =========================================================
        $hasGlobalPatrolAccess = $user->can('patrol-report manage-all');

        // Query dasar (scoping akses lokasi) TANPA filter tanggal, dipakai
        // ulang baik untuk statistik hari ini maupun tabel aktivitas terbaru.
        $scopedPatrolQuery = PatrolReport::query();

        if (!$hasGlobalPatrolAccess) {
            $allowedLocationIds = Location::all()->filter(function ($location) use ($user) {
                return Enforcer::guard('basic')->enforce(strval($user->id), 'location_' . $location->id, 'view');
            })->pluck('id');

            $scopedPatrolQuery->where(function ($q) use ($user, $allowedLocationIds) {
                $q->where('user_id', $user->id)
                    ->orWhereIn('location_id', $allowedLocationIds);
            });
        }

        $patrolQuery = (clone $scopedPatrolQuery)->whereDate('patrol_at', $today);

        $activePatrolCount = (clone $patrolQuery)->count();

        $ongoingIncidentCount = (clone $patrolQuery)
            ->whereIn('status', ['mencurigakan', 'temuan_darurat'])
            ->count();

        // =========================================================
        // 3. Personil Bertugas — sedang clock-in, belum clock-out.
        //    Scoping mengikuti pola AttendanceController::index():
        //    permission 'attendance view all' + ReBAC action 'view'.
        // =========================================================
        $canViewAllAttendance = $user->can('attendance view all');
        $supervisedLocationIds = [];

        if (!$canViewAllAttendance) {
            try {
                $policyRules = Enforcer::guard('basic')->getFilteredGroupingPolicy(0, strval($user->id), 'view');

                if (is_array($policyRules)) {
                    foreach ($policyRules as $rule) {
                        if (isset($rule[2])) {
                            $supervisedLocationIds[] = (int) str_replace('location_', '', $rule[2]);
                        }
                    }
                }
            } catch (\Exception $e) {
                $supervisedLocationIds = [];
            }
        }

        $onDutyQuery = Attendance::whereDate('date', $today)
            ->whereNotNull('clock_in_at')
            ->whereNull('clock_out_at');

        if (!$canViewAllAttendance) {
            $onDutyQuery->where(function ($q) use ($user, $supervisedLocationIds) {
                $q->where('user_id', $user->id);
                if (!empty($supervisedLocationIds)) {
                    $q->orWhereIn('location_id', $supervisedLocationIds);
                }
            });
        }

        $onDutyCount = $onDutyQuery->count();

        // =========================================================
        // 4. Cakupan Area — diambil LANGSUNG dari kolom radius_meters
        //    milik Location (diisi lewat form create/edit pos di
        //    LocationController, kolom 'radius', min 50 meter).
        //    Ditampilkan sebagai rata-rata radius jangkauan (meter) dari
        //    pos yang ada dalam cakupan user, bukan persentase lagi.
        //    Dasar pos yang dihitung tetap ikut scoping absensi
        //    (supervisedLocationIds) supaya relevan dengan pos yang
        //    memang jadi tanggung jawab user.
        // =========================================================
        $locationBaseQuery = Location::query();

        if (!$canViewAllAttendance && !empty($supervisedLocationIds)) {
            $locationBaseQuery->whereIn('id', $supervisedLocationIds);
        }

        // CATATAN: kolom yang dipakai di sini adalah 'radius', mengikuti
        // nama field yang divalidasi & disimpan LocationController::store()/
        // update(). Kalau nama kolom asli di DB kamu ternyata 'radius_meters'
        // (seperti yang dipakai AttendanceController::clockIn()), ganti
        // 'radius' di bawah ini jadi 'radius_meters'.
        $averageRadiusMeters = (int) round(
            (clone $locationBaseQuery)->avg('radius') ?? 0
        );

        // =========================================================
        // 5. Aktivitas Laporan Patroli Terakhir (5 terbaru)
        //    Scoping SAMA PERSIS dengan query poin 1 & 2 di atas
        //    (patrolQuery), supaya user tidak melihat laporan dari
        //    pos yang tidak dia punya akses 'view'-nya.
        //    Catatan: PatrolReportController belum punya route 'show'
        //    per-laporan, jadi tombol aksi mengarah ke halaman index
        //    laporan patroli dengan filter location_id (parameter yang
        //    sudah didukung PatrolReportController::index()).
        // =========================================================
        $recentPatrolReports = (clone $scopedPatrolQuery)
            ->with(['user', 'location'])
            ->latest('patrol_at')
            ->paginate(2, ['*'], 'patrol_page')   // 2 data per halaman
            ->withQueryString()                   // parameter URL lain tetap terbawa
            ->fragment('aktivitas-patroli');      // setelah pindah halaman, scroll tetap di tabel

        return view('dashboard.index', [
            'activePatrolCount'    => $activePatrolCount,
            'ongoingIncidentCount' => $ongoingIncidentCount,
            'onDutyCount'          => $onDutyCount,
            'averageRadiusMeters'  => $averageRadiusMeters,
            'recentPatrolReports'  => $recentPatrolReports,
        ]);
    }
}
