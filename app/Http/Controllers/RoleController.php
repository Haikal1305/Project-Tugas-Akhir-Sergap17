<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Role yang tidak boleh diubah/dihapus lewat UI (role inti sistem).
     */
    protected array $protectedRoles = ['super-admin'];

    /**
     * Menampilkan daftar role.
     */
    public function index(Request $request)
    {
        $roles = Role::withCount(['permissions', 'users'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->string('search') . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('roles.index', compact('roles'));
    }

    /**
     * Menampilkan form tambah role.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Menyimpan role baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
        ]);

        Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit nama role.
     */
    public function edit(Role $role)
    {
        if (in_array($role->name, $this->protectedRoles)) {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Role ini tidak dapat diubah.');
        }

        return view('roles.edit', compact('role'));
    }

    /**
     * Update nama role.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        if (in_array($role->name, $this->protectedRoles)) {
            return back()->withErrors([
                'role' => 'Role ini tidak dapat diubah.',
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Menghapus role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, $this->protectedRoles)) {
            return back()->withErrors([
                'role' => 'Role ini tidak dapat dihapus.',
            ]);
        }

        if ($role->users()->count() > 0) {
            return back()->withErrors([
                'role' => 'Role tidak dapat dihapus karena masih digunakan oleh user.',
            ]);
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}