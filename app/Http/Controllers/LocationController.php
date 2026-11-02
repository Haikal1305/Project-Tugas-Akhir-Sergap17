<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Lauthz\Facades\Enforcer;
use Spatie\Permission\Models\Role;

class LocationController extends Controller
{
    /**
     * Daftar relasi/aksi yang tersedia untuk otorisasi administratif per-lokasi (ReBAC).
     * Dipakai bareng di editSupervisors() & updateSupervisors().
     */
    protected array $managedActions = ['view', 'update', 'delete', 'assign', 'export'];

    public function index()
    {
        $user = auth()->user();

        // Memuat relasi users beserta data pivot (shift)
        $query = Location::with('users')->withCount('users')->latest();

        $isFullAccess = $user->can('location manage-all');

        // Peta hak per pos untuk user ini: [location_id => ['view', 'update', ...]]
        // Diambil dari relasi ReBAC (saklar di halaman Hak Kelola Pos).
        $abilitiesByLocation = [];

        if (!$isFullAccess) {
            try {
                $rules = Enforcer::guard('basic')->getFilteredGroupingPolicy(0, strval($user->id));

                foreach ($rules as $rule) {
                    if (isset($rule[1], $rule[2]) && str_starts_with($rule[2], 'location_')) {
                        $locationId = (int) str_replace('location_', '', $rule[2]);
                        $abilitiesByLocation[$locationId][] = $rule[1];
                    }
                }
            } catch (\Exception $e) {
                $abilitiesByLocation = [];
            }

            // User biasa hanya melihat pos yang punya relasi 'view'
            $viewableIds = collect($abilitiesByLocation)
                ->filter(fn ($actions) => in_array('view', $actions, true))
                ->keys()
                ->all();

            $query->whereIn('id', $viewableIds);
        }

        $locations = $query->paginate(10);

        // Admin pusat: semua aksi di semua pos
        if ($isFullAccess) {
            foreach ($locations as $location) {
                $abilitiesByLocation[$location->id] = $this->managedActions;
            }
        }

        return view('locations.index', compact('locations', 'abilitiesByLocation'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'required|integer|min:50',
        ]);

        Location::create($validated);

        return redirect()->route('locations.index')->with('success', 'Pos jaga berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        $this->authorizeLocation($location, 'update');

        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $this->authorizeLocation($location, 'update');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'required|integer|min:50',
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')->with('success', 'Pos jaga berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        $this->authorizeLocation($location, 'delete');

        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Pos jaga berhasil dihapus.');
    }

    /**
     * ==============================
     * Penugasan SATPAM ke Pos (pivot location_user)
     * ==============================
     */
    public function editAssignment(Location $location)
    {
        $this->authorizeLocation($location, 'assign');

        $users = User::permission('attendance create')->get();

        // Ambil data user yang sudah terpaut beserta informasi shift-nya
        $assignedUsers = $location->users()->get()->keyBy('id');

        return view('locations.assignment', compact('location', 'users', 'assignedUsers'));
    }

    public function updateAssignment(Request $request, Location $location)
    {
        $this->authorizeLocation($location, 'assign');

        $validated = $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'shifts' => 'nullable|array',
            'shifts.*.start' => 'nullable|date_format:H:i',
            'shifts.*.end' => 'nullable|date_format:H:i',
        ]);

        $syncData = [];
        $userIds = $validated['user_ids'] ?? [];
        $shifts = $validated['shifts'] ?? [];

        foreach ($userIds as $userId) {
            $syncData[$userId] = [
                'assigned_date' => now()->toDateString(),
                'shift_start' => $shifts[$userId]['start'] ?? null,
                'shift_end' => $shifts[$userId]['end'] ?? null,
            ];
        }

        // Sinkronkan data ke tabel pivot location_user
        $location->users()->sync($syncData);

        return redirect()->route('locations.index')->with('success', 'Penugasan personel dan jam shift berhasil diperbarui.');
    }

    /**
     * ==============================
     * Hak Kelola Pos untuk SUPERVISOR/ADMIN (Casbin ReBAC Murni)
     * Selalu eksplisit Enforcer::guard('basic') — jangan pakai bare Enforcer::,
     * karena guard 'attendance' juga aktif di aplikasi ini (dipakai Attendance
     * & PatrolReport) dan model-nya TIDAK punya [role_definition]/g, sehingga
     * addGroupingPolicy/hasGroupingPolicy akan crash kalau ke-resolve ke guard salah.
     *
     * User dengan permission 'location manage-all' (termasuk super-admin lewat
     * Gate::before) dianggap punya AKSES PENUH ke semua pos, jadi tidak ditampilkan
     * saklar per-aksi dan policy ReBAC-nya tidak disentuh.
     * ==============================
     */
    public function editSupervisors(Request $request, Location $location)
    {
        $user = auth()->user();

        if (!$user->can('location manage-all')) {
            abort(403, 'Akses Ditolak: Hanya admin pusat yang dapat mengatur hak kelola pos.');
        }

        $roles = Role::pluck('name');
        $selectedRole = $request->query('role', $roles->first());

        $candidates = $selectedRole
            ? User::role($selectedRole)->get()
            : collect();

        $obj = 'location_' . $location->id;

        // Tandai user yang punya akses penuh (super-admin + pemegang 'location manage-all')
        $fullAccess = [];
        foreach ($candidates as $candidate) {
            $fullAccess[$candidate->id] = $candidate->can('location manage-all');
        }

        // Matrix saklar hanya untuk user TANPA akses penuh
        $matrix = [];
        foreach ($candidates as $candidate) {
            if ($fullAccess[$candidate->id]) {
                continue;
            }

            foreach ($this->managedActions as $action) {
                // REBAC: Mengecek apakah user memiliki "hubungan (role)" $action pada objek/domain $obj
                // Format parameter Casbin untuk ReBAC: g(user, role, domain/object)
                $matrix[$candidate->id][$action] = Enforcer::guard('basic')->hasGroupingPolicy(
                    strval($candidate->id),
                    $action,
                    $obj
                );
            }
        }

        // Ringkasan: ROLE dengan akses penuh (super-admin + role pemegang 'location manage-all')
        $fullAccessRoles = Role::where('name', 'super-admin')
            ->orWhereHas('permissions', fn ($q) => $q->where('name', 'location manage-all'))
            ->orderBy('name')
            ->pluck('name');

        // ID user berakses penuh (dipakai untuk menyaring daftar hak khusus di bawah)
        $fullAccessUserIds = User::role('super-admin')->pluck('id')
            ->merge(User::permission('location manage-all')->pluck('id'))
            ->unique()
            ->values();

        // Ringkasan: user dengan hak khusus pos ini (ReBAC), dikelompokkan per user
        // Policy g: [user, action, obj] -> filter kolom index 2 = $obj
        $rebacByUser = collect(Enforcer::guard('basic')->getFilteredGroupingPolicy(2, $obj))
            ->groupBy(0)
            ->map(fn ($rows) => $rows->pluck(1)->all());

        $rebacUsers = User::whereIn('id', $rebacByUser->keys()->all())
            ->whereNotIn('id', $fullAccessUserIds)
            ->get();

        return view('locations.supervisors', [
            'location' => $location,
            'roles' => $roles,
            'selectedRole' => $selectedRole,
            'candidates' => $candidates,
            'matrix' => $matrix,
            'fullAccess' => $fullAccess,
            'fullAccessRoles' => $fullAccessRoles,
            'rebacUsers' => $rebacUsers,
            'rebacByUser' => $rebacByUser,
            'actions' => $this->managedActions,
        ]);
    }

    public function updateSupervisors(Request $request, Location $location)
    {
        $user = auth()->user();

        if (!$user->can('location manage-all')) {
            abort(403, 'Akses Ditolak: Hanya admin pusat yang dapat mengatur hak kelola pos.');
        }

        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'array',
            'permissions.*.*' => 'in:' . implode(',', $this->managedActions),
        ]);

        $submitted = $validated['permissions'] ?? [];
        $obj = 'location_' . $location->id;

        $candidates = User::role($validated['role'])->get();

        foreach ($candidates as $candidate) {
            // User berakses penuh tidak perlu policy per-pos, lewati
            if ($candidate->can('location manage-all')) {
                continue;
            }

            $userId = $candidate->id;
            $grantedActions = $submitted[$userId] ?? [];

            foreach ($this->managedActions as $action) {
                // Cek relasi ReBAC saat ini
                $has = Enforcer::guard('basic')->hasGroupingPolicy(strval($userId), $action, $obj);
                $shouldHave = in_array($action, $grantedActions, true);

                if ($shouldHave && !$has) {
                    // REBAC: Menambahkan relasi user -> aksi -> objek
                    Enforcer::guard('basic')->addGroupingPolicy(strval($userId), $action, $obj);
                } elseif (!$shouldHave && $has) {
                    // REBAC: Menghapus relasi
                    Enforcer::guard('basic')->removeGroupingPolicy(strval($userId), $action, $obj);
                }
            }
        }

        return redirect()
            ->route('locations.supervisors.edit', ['location' => $location->id, 'role' => $validated['role']])
            ->with('success', 'Hak akses pengelolaan pos berhasil diperbarui.');
    }

    /**
     * Halaman BACA-SAJA: siapa yang punya akses ke pos ini.
     * Boleh dibuka siapa pun yang punya relasi ReBAC 'view' pada pos ini
     * (admin pusat lewat bypass di authorizeLocation). Tidak ada aksi ubah,
     * dan email user sengaja tidak ditampilkan.
     */
    public function showAccess(Location $location)
    {
        $this->authorizeLocation($location, 'view');

        $obj = 'location_' . $location->id;

        // ROLE dengan akses penuh (super-admin + role pemegang 'location manage-all')
        $fullAccessRoles = Role::where('name', 'super-admin')
            ->orWhereHas('permissions', fn ($q) => $q->where('name', 'location manage-all'))
            ->orderBy('name')
            ->pluck('name');

        // ID user berakses penuh (disaring dari daftar hak khusus)
        $fullAccessUserIds = User::role('super-admin')->pluck('id')
            ->merge(User::permission('location manage-all')->pluck('id'))
            ->unique()
            ->values();

        // Hak khusus pos ini (ReBAC), dikelompokkan per user: [user_id => ['view', ...]]
        $rebacByUser = collect(Enforcer::guard('basic')->getFilteredGroupingPolicy(2, $obj))
            ->groupBy(0)
            ->map(fn ($rows) => $rows->pluck(1)->all());

        $rebacUsers = User::whereIn('id', $rebacByUser->keys()->all())
            ->whereNotIn('id', $fullAccessUserIds)
            ->get();

        return view('locations.access', [
            'location' => $location,
            'fullAccessRoles' => $fullAccessRoles,
            'rebacUsers' => $rebacUsers,
            'rebacByUser' => $rebacByUser,
            'actions' => $this->managedActions,
        ]);
    }

    /**
     * Helper: cek otorisasi administratif atas satu lokasi.
     */
    protected function authorizeLocation(Location $location, string $action): void
    {
        $user = auth()->user();

        if ($user->can('location manage-all')) {
            return;
        }

        // Enforce tetap sama, Casbin yang akan mencocokkan melalui Model ReBAC
        if (!Enforcer::guard('basic')->enforce(strval($user->id), 'location_' . $location->id, $action)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki akses untuk aksi ini pada pos tersebut.');
        }
    }
}