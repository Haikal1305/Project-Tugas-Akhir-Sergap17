<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\PatrolReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lauthz\Facades\Enforcer;
use Carbon\Carbon;

class PatrolReportController extends Controller
{
    private const ALLOWED_PHOTO_EXTENSIONS = ['jpg', 'jpeg', 'png'];
    private const MAX_PHOTO_SIZE = 5 * 1024 * 1024; // 5MB

    /**
     * Tampilkan daftar laporan patroli.
     * Otorisasi per-lokasi memakai relasi ReBAC lokasi (guard 'basic').
     *
     * Desain: hanya ADA SATU permission Spatie global di sini, 'patrol-report
     * manage-all', untuk admin pusat yang bypass semua pengecekan ReBAC.
     * Selain itu, cakupan lihat laporan SELALU = laporan milik sendiri +
     * laporan dari lokasi yang di-grant lewat ReBAC (halaman Hak Kelola Pos).
     * Tidak ada lagi permission 'patrol-report view all' terpisah — itu
     * duplikat fungsi dengan ReBAC dan sudah dihapus.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $hasGlobalAccess = $user->can('patrol-report manage-all');
        $hasManagedLocations = false;

        $query = PatrolReport::with(['user', 'location']);

        if (!$hasGlobalAccess) {
            $allowedLocationIds = Location::all()->filter(function ($location) use ($user) {
                return Enforcer::guard('basic')->enforce(strval($user->id), 'location_' . $location->id, 'view');
            })->pluck('id');

            $hasManagedLocations = $allowedLocationIds->isNotEmpty();

            $query->where(function ($q) use ($user, $allowedLocationIds) {
                $q->where('user_id', $user->id)
                    ->orWhereIn('location_id', $allowedLocationIds);
            });
        }

        if ($request->filled('location_id')) {
            $this->authorizeLocationAccess((int) $request->location_id, 'view');
            $query->where('location_id', $request->location_id);
        }

        $reports = $query->latest('patrol_at')->paginate(15);

        $locations = Location::all();
        return view('patrol-reports.index', compact('reports', 'locations', 'hasManagedLocations'));
    }
    
    public function create()
    {
        $user = Auth::user();
        $locations = $user->locations;

        return view('patrol-reports.create', compact('locations'));
    }

    /**
     * Proses simpan laporan patroli.
     * Otorisasi ReBAC (assignment) + ABAC (status kehadiran & jam shift)
     * lewat guard 'attendance', action 'patrol_create'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'report'      => 'required|string|max:1000',
            'status'      => 'required|string|in:aman,mencurigakan,temuan_darurat',
            'photo'       => 'nullable|string',
            'lat'         => 'nullable|numeric|between:-90,90',
            'lng'         => 'nullable|numeric|between:-180,180',
        ]);

        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();

        $location = Location::findOrFail($request->location_id);

        $pivotData = $location->users()->where('users.id', $user->id)->first()?->pivot;

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->whereNotNull('clock_in_at')
            ->first();

        $subAttr = (object) [
            'id'       => $user->id,
            'assigned' => $pivotData !== null,
            'on_duty'  => $attendance && $attendance->clock_in_at && !$attendance->clock_out_at,
        ];

        $objAttr = (object) [
            'id'          => $location->id,
            'shift_start' => $pivotData->shift_start ?? '',
            'shift_end'   => $pivotData->shift_end ?? '',
        ];

        $envAttr = (object) [
            'now' => $now->format('H:i'),
        ];

        if (!Enforcer::guard('attendance')->enforce($subAttr, $objAttr, 'patrol_create', $envAttr)) {
            if (!$subAttr->assigned) {
                return back()->with('error', 'Anda tidak ditugaskan di pos ini.');
            }
            if (!$subAttr->on_duty) {
                return back()->with('error', 'Akses ditolak: Anda harus melakukan clock-in terlebih dahulu dan belum melakukan clock-out.');
            }
            return back()->with('error', "Laporan patroli hanya dapat dikirim selama jam kerja aktif shift ({$objAttr->shift_start} - {$objAttr->shift_end}).");
        }

        $photoPath = null;
        if ($request->filled('photo')) {
            try {
                $photoPath = $this->decodeAndStorePhoto($request->input('photo'), 'patrol-reports');
            } catch (\InvalidArgumentException $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        PatrolReport::create([
            'user_id'     => $user->id,
            'location_id' => $location->id,
            'date'        => $today,
            'patrol_at'   => $now,
            'report'      => $request->report,
            'status'      => $request->status,
            'photo'       => $photoPath,
            'lat'         => $request->lat,
            'lng'         => $request->lng,
        ]);

        return redirect()->route('patrol-reports.index')->with('success', 'Laporan patroli berhasil dikirim.');
    }

    public function destroy(PatrolReport $patrolReport)
    {
        $user = Auth::user();

        if ($patrolReport->user_id !== $user->id && !$user->can('patrol-report delete')) {
            abort(403, 'Unauthorized action.');
        }

        if ($patrolReport->photo && Storage::disk('public')->exists($patrolReport->photo)) {
            Storage::disk('public')->delete($patrolReport->photo);
        }

        $patrolReport->delete();

        return back()->with('success', 'Laporan patroli berhasil dihapus.');
    }

    /**
     * Cek akses 'view'/'export' laporan patroli suatu lokasi via relasi ReBAC lokasi.
     */
    protected function authorizeLocationAccess(int $locationId, string $action): void
    {
        $user = Auth::user();

        if ($user->can('patrol-report manage-all')) {
            return;
        }

        if (!Enforcer::guard('basic')->enforce(strval($user->id), 'location_' . $locationId, $action)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses terhadap laporan di pos ini.');
        }
    }

    private function decodeAndStorePhoto(string $base64DataUri, string $folder): string
    {
        if (!str_contains($base64DataUri, ';base64,')) {
            throw new \InvalidArgumentException('Format foto tidak valid.');
        }

        [$header, $encoded] = explode(';base64,', $base64DataUri, 2);

        if (!preg_match('/^data:image\/(jpeg|jpg|png)$/', $header, $matches)) {
            throw new \InvalidArgumentException('Foto harus berformat JPEG atau PNG.');
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];

        if (!in_array($extension, self::ALLOWED_PHOTO_EXTENSIONS, true)) {
            throw new \InvalidArgumentException('Format foto tidak diizinkan.');
        }

        $decoded = base64_decode($encoded, true);

        if ($decoded === false || strlen($decoded) > self::MAX_PHOTO_SIZE) {
            throw new \InvalidArgumentException('Data foto rusak atau ukuran terlalu besar (maksimal 5MB).');
        }

        $fileName = trim($folder, '/') . '/' . uniqid('patrol_', true) . '.' . $extension;
        Storage::disk('public')->put($fileName, $decoded); // fix: sebelumnya "$Storage::" (typo, crash)

        return $fileName;
    }
}
