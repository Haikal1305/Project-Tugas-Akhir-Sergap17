<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * Role yang permission-nya tidak boleh diubah lewat UI
     * (super-admin sudah full-access lewat Gate::before).
     */
    protected array $protectedRoles = ['super-admin'];

    /**
     * Label modul (kata pertama nama permission) yang ramah dibaca Admin.
     * Kalau ada modul baru yang belum masuk sini, otomatis di-Title Case
     * lewat fallbackModuleLabel() supaya tidak hilang dari tampilan.
     */
    protected array $moduleLabels = [
        'attendance'    => 'Absensi',
        'leave-request' => 'Pengajuan Izin',
        'location'      => 'Kontrol Lokasi',
        'patrol'        => 'Monitoring Patroli',
        'report'        => 'Pelaporan',
        'user'          => 'Data Personil',
        'role'          => 'Manajemen Role',
    ];

    /**
     * Label nama tiap permission satuan, supaya tidak tampil mentah
     * seperti "attendance view all" di depan Admin.
     *
     * PENTING: kalau nambah permission baru di seeder/migration,
     * tambahkan juga terjemahannya di sini. Kalau lupa, sistem tetap
     * jalan (fallback ke humanizePermissionName()), cuma tampilannya
     * kurang rapi.
     */
    protected array $permissionLabels = [
        // Absensi
        'attendance create'   => 'Tambah Data Absensi',
        'attendance view'     => 'Lihat Data Absensi',
        'attendance delete'   => 'Hapus Data Absensi',
        'attendance view all' => 'Lihat Semua Data Absensi (Semua Pos)',

        // Pengajuan Izin
        'leave-request create'   => 'Ajukan Izin',
        'leave-request view'     => 'Lihat Pengajuan Izin',
        'leave-request review'   => 'Setujui / Tolak Pengajuan Izin',
        'leave-request delete'   => 'Hapus Pengajuan Izin',
        'leave-request view all' => 'Lihat Semua Pengajuan Izin (Semua Pos)',

        // Kontrol Lokasi
        'location assign'     => 'Tetapkan Pos / Lokasi',
        'location create'     => 'Tambah Lokasi',
        'location update'     => 'Ubah Lokasi',
        'location delete'     => 'Hapus Lokasi',
        'location view'       => 'Lihat Lokasi',
        'location manage-all' => 'Kelola Semua Lokasi (Semua Pos)',
    ];

    /**
     * Kata kerja yang dipakai untuk membangun nama otomatis
     * (fallback) jika permission belum ada di $permissionLabels.
     */
    protected array $actionWords = [
        'create'     => 'Tambah',
        'view'       => 'Lihat',
        'view-all'   => 'Lihat Semua',
        'all'        => 'Semua',
        'update'     => 'Ubah',
        'edit'       => 'Ubah',
        'delete'     => 'Hapus',
        'assign'     => 'Tetapkan',
        'review'     => 'Tinjau',
        'approve'    => 'Setujui',
        'reject'     => 'Tolak',
        'manage'     => 'Kelola',
        'manage-all' => 'Kelola Semua',
        'download'   => 'Unduh',
        'upload'     => 'Unggah',
        'generate'   => 'Buat',
        'reset'      => 'Reset',
    ];

    /**
     * Menampilkan form kelola permission untuk sebuah role.
     */
    public function edit(Role $role)
    {
        if (in_array($role->name, $this->protectedRoles)) {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Permission role ini tidak dapat diubah.');
        }

        $permissions = $this->groupPermissions(Permission::orderBy('name')->get());
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update permission untuk sebuah role.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        if (in_array($role->name, $this->protectedRoles)) {
            return back()->withErrors([
                'permissions' => 'Permission role ini tidak dapat diubah.',
            ]);
        }

        $validated = $request->validate([
            'permissions'   => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        // Sync: permission yang tidak dicentang otomatis dilepas dari role
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', "Permission role \"{$role->name}\" berhasil diperbarui.");
    }

    /**
     * Kelompokkan permission per modul, sekaligus tempelkan properti
     * ->label dan ->isBroadAccess ke tiap Permission supaya view tinggal
     * pakai $permission->label, tanpa perlu ubah struktur data lama.
     */
    private function groupPermissions(Collection $permissions): array
    {
        return $permissions
            ->map(function (Permission $permission) {
                $permission->label = $this->permissionLabels[$permission->name]
                    ?? $this->humanizePermissionName($permission->name);

                $permission->isBroadAccess = str_ends_with($permission->name, 'manage-all')
                    || str_ends_with($permission->name, 'view all');

                return $permission;
            })
            ->groupBy(function (Permission $permission) {
                return explode(' ', $permission->name)[0];
            })
            ->sortKeys()
            ->mapWithKeys(function (Collection $modulePermissions, string $module) {
                $label = $this->moduleLabels[$module] ?? $this->fallbackModuleLabel($module);

                return [$label => $modulePermissions->values()];
            })
            ->all(); // sengaja pakai all(), BUKAN toArray() — toArray() bersifat
                     // rekursif dan akan ikut mengubah tiap Permission jadi array,
                     // sehingga $permission->id di view menjadi $permission['id']
                     // dan melempar "Attempt to read property on array".
    }

    /**
     * Ubah nama permission mentah (misal: "report generate")
     * menjadi kalimat yang enak dibaca (misal: "Buat Pelaporan"),
     * dipakai kalau permission belum didaftarkan di $permissionLabels.
     */
    private function humanizePermissionName(string $name): string
    {
        $words  = explode(' ', $name);
        $module = array_shift($words);
        $action = implode(' ', $words); // sisanya, misal: "view all" / "manage-all"

        $actionLabel = $this->actionWords[$action] ?? Str::title(str_replace('-', ' ', $action));
        $moduleLabel = $this->moduleLabels[$module] ?? $this->fallbackModuleLabel($module);

        return trim("{$actionLabel} {$moduleLabel}");
    }

    /**
     * Fallback label modul kalau belum didaftarkan di $moduleLabels,
     * biar permission baru tetap tampil rapi (bukan "attendance" mentah).
     */
    private function fallbackModuleLabel(string $module): string
    {
        return Str::title(str_replace('-', ' ', $module));
    }
}