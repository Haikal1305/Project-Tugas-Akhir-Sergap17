<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Lauthz\Facades\Enforcer;

class AttendanceController extends Controller
{
    /**
     * Ekstensi foto yang diizinkan.
     */
    private const ALLOWED_PHOTO_EXTENSIONS = ['jpg', 'jpeg', 'png'];

    /**
     * Ukuran maksimal foto (dalam byte) setelah didecode. 5MB.
     */
    private const MAX_PHOTO_SIZE = 5 * 1024 * 1024;

    /**
     * Radius default (meter) jika lokasi tidak punya radius sendiri.
     */
    private const DEFAULT_RADIUS_METERS = 100;

    /**
     * Tampilkan riwayat absensi.
     */
    public function index()
    {
        $user = Auth::user();
        $hasManagedLocations = false;

        // 1. Super Admin / Admin Pusat
        if ($user->can('attendance view all')) {
            $attendances = Attendance::with(['user', 'location'])->latest('date')->paginate(15);
        } else {
            // 2. Cek pos mana saja yang boleh dipantau oleh user ini via ReBAC Casbin
            $supervisedLocationIds = [];

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

            $hasManagedLocations = !empty($supervisedLocationIds);

            // 3. Absensi miliknya SENDIRI + absensi POS YANG DIA SUPERVISI
            $attendances = Attendance::with(['user', 'location'])
                ->where(function ($query) use ($user, $supervisedLocationIds) {
                    $query->where('user_id', $user->id);

                    if (!empty($supervisedLocationIds)) {
                        $query->orWhereIn('location_id', $supervisedLocationIds);
                    }
                })
                ->latest('date')
                ->paginate(15);
        }

        return view('attendances.index', compact('attendances', 'hasManagedLocations'));
    }

    /**
     * Halaman antarmuka absensi.
     */
    public function create()
    {
        $user = Auth::user();

        // Lokasi tempat user ini ditugaskan sebagai satpam
        $locations = $user->locations;

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        return view('attendances.create', compact('locations', 'todayAttendance'));
    }

    /**
     * Proses Clock In (Masuk).
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'lat'         => 'required|numeric|between:-90,90',
            'lng'         => 'required|numeric|between:-180,180',
            'photo'       => 'required|string',
        ]);

        $user = Auth::user();
        $today = Carbon::today();
        $location = Location::findOrFail($request->location_id);

        // ReBAC: relasi penugasan user <-> lokasi (pivot location_user)
        $pivotData = $this->getPivot($location, $user->id);

        // ABAC: sub, obj, env harus object agar r.sub.assigned / r.env.lat bisa dievaluasi
        [$subAttr, $objAttr, $envAttr] = $this->buildAbacAttributes($user, $location, $pivotData, $request);

        if (!Enforcer::guard('attendance')->enforce($subAttr, $objAttr, 'clock_in', $envAttr)) {
            Log::info('Clock-in ditolak Casbin', [
                'sub' => $subAttr,
                'obj' => $objAttr,
                'env' => $envAttr,
            ]);

            return back()->with('error', 'Akses Ditolak: Anda tidak ditugaskan di pos ini ATAU berada di luar radius jangkauan pos.');
        }

        try {
            $photoPath = $this->decodeAndStorePhoto($request->input('photo'), 'attendances/clock-in');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return DB::transaction(function () use ($user, $today, $location, $pivotData, $photoPath, $request) {
            // Kunci baris attendance hari ini untuk cegah double clock-in
            $existing = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->clock_in_at) {
                return back()->with('error', 'Anda sudah melakukan clock-in hari ini.');
            }

            $now = Carbon::now();
            $status = 'hadir';
            $note = null;

            // Kalkulasi keterlambatan berdasarkan jam shift_start
            if ($pivotData && $pivotData->shift_start) {
                $shiftStartTime = Carbon::today()->setTimeFromTimeString($pivotData->shift_start);

                if ($now->greaterThan($shiftStartTime)) {
                    $status = 'terlambat';
                    $selisih = (int) round($shiftStartTime->diffInMinutes($now));
                    $note = "Clock-in pukul {$now->format('H:i')}, terlambat {$selisih} menit dari jadwal shift ({$shiftStartTime->format('H:i')}).";
                }
            }

            Attendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date'    => $today,
                ],
                [
                    'location_id'    => $location->id,
                    'clock_in_at'    => $now,
                    'status'         => $status,
                    'notes'          => $note,
                    'clock_in_lat'   => $request->lat,
                    'clock_in_lng'   => $request->lng,
                    'clock_in_photo' => $photoPath,
                ]
            );

            return redirect()->route('attendances.index')->with('success', 'Berhasil melakukan clock-in!');
        });
    }

    /**
     * Proses Clock Out (Keluar).
     */
    public function clockOut(Request $request, Attendance $attendance)
    {
        $request->validate([
            'lat'   => 'required|numeric|between:-90,90',
            'lng'   => 'required|numeric|between:-180,180',
            'photo' => 'required|string',
        ]);

        if ($attendance->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attendance->clock_out_at) {
            return back()->with('error', 'Anda sudah melakukan clock-out hari ini.');
        }

        $location = $attendance->location;
        $user = Auth::user();
        $pivotData = null;

        // ABAC: validasi penugasan & radius clock-out via Casbin
        if ($location) {
            $pivotData = $this->getPivot($location, $user->id);

            [$subAttr, $objAttr, $envAttr] = $this->buildAbacAttributes($user, $location, $pivotData, $request);

            if (!Enforcer::guard('attendance')->enforce($subAttr, $objAttr, 'clock_out', $envAttr)) {
                Log::info('Clock-out ditolak Casbin', [
                    'sub' => $subAttr,
                    'obj' => $objAttr,
                    'env' => $envAttr,
                ]);

                return back()->with('error', 'Akses Ditolak: Lokasi clock-out di luar radius pos atau Anda tidak terdaftar.');
            }
        }

        try {
            $photoPath = $this->decodeAndStorePhoto($request->input('photo'), 'attendances/clock-out');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        $now = Carbon::now();
        $status = $attendance->status;
        $noteAppend = null;

        if ($location && $pivotData && $pivotData->shift_end) {
            $shiftEndTime = Carbon::today()->setTimeFromTimeString($pivotData->shift_end);

            if ($now->lessThan($shiftEndTime)) {
                $status = 'pulang_cepat';
                $selisih = (int) round($now->diffInMinutes($shiftEndTime));
                $noteAppend = "Pulang Cepat: Keluar pukul {$now->format('H:i')} (Lebih awal {$selisih} menit dari jadwal {$shiftEndTime->format('H:i')}).";
            }
        }

        $attendance->update([
            'clock_out_at'    => $now,
            'clock_out_lat'   => $request->lat,
            'clock_out_lng'   => $request->lng,
            'clock_out_photo' => $photoPath,
            'status'          => $status,
            'notes'           => $noteAppend
                ? trim(($attendance->notes ? $attendance->notes . ' | ' : '') . $noteAppend)
                : $attendance->notes,
        ]);

        return redirect()->route('attendances.index')->with('success', 'Berhasil melakukan clock-out. Shift selesai!');
    }

    public function destroy(Attendance $attendance)
    {
        $user = Auth::user();

        if (!$user->can('attendance delete')) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak untuk menghapus data absensi.');
        }

        // ReBAC scoping: admin pusat bebas lintas pos, selain itu wajib punya relasi 'delete'
        if (!$user->can('location manage-all')) {
            $obj = 'location_' . $attendance->location_id;

            if (!Enforcer::guard('basic')->hasGroupingPolicy(strval($user->id), 'delete', $obj)) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki hak kelola pos untuk absensi ini.');
            }
        }

        if ($attendance->clock_in_photo && Storage::disk('public')->exists($attendance->clock_in_photo)) {
            Storage::disk('public')->delete($attendance->clock_in_photo);
        }

        if ($attendance->clock_out_photo && Storage::disk('public')->exists($attendance->clock_out_photo)) {
            Storage::disk('public')->delete($attendance->clock_out_photo);
        }

        $attendance->delete();

        return back()->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Helper: ambil data pivot penugasan user pada lokasi (null jika tidak ditugaskan).
     */
    private function getPivot(Location $location, int $userId)
    {
        return $location->users()->where('users.id', $userId)->first()?->pivot;
    }

    /**
     * Helper: bentuk atribut subject, object, environment untuk ABAC Casbin.
     * Dipakai bersama oleh clockIn & clockOut supaya konsisten.
     *
     * Radius dibaca dari kolom `radius` (sesuai LocationController).
     * Fallback ke `radius_meters` jika suatu saat kolom itu yang dipakai,
     * lalu ke DEFAULT_RADIUS_METERS.
     */
    private function buildAbacAttributes($user, Location $location, $pivotData, Request $request): array
    {
        $subAttr = (object) [
            'id'       => $user->id,
            'assigned' => $pivotData !== null, // hasil lookup ReBAC dipakai sbg atribut ABAC
        ];

        $objAttr = (object) [
            'id'     => $location->id,
            'lat'    => (float) $location->lat,
            'lng'    => (float) $location->lng,
            'radius' => (float) ($location->radius ?? $location->radius_meters ?? self::DEFAULT_RADIUS_METERS),
        ];

        $envAttr = (object) [
            'lat' => (float) $request->lat,
            'lng' => (float) $request->lng,
        ];

        return [$subAttr, $objAttr, $envAttr];
    }

    /**
     * Helper: Decode string Base64 (data URI) menjadi file gambar fisik di storage.
     */
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

        if ($decoded === false) {
            throw new \InvalidArgumentException('Data foto rusak atau tidak valid.');
        }

        if (strlen($decoded) > self::MAX_PHOTO_SIZE) {
            throw new \InvalidArgumentException('Ukuran foto terlalu besar (maksimal 5MB).');
        }

        $imageInfo = @getimagesizefromstring($decoded);
        if ($imageInfo === false) {
            throw new \InvalidArgumentException('File yang dikirim bukan gambar yang valid.');
        }

        $fileName = trim($folder, '/') . '/' . uniqid('att_', true) . '.' . $extension;
        Storage::disk('public')->put($fileName, $decoded);

        return $fileName;
    }
}