<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as Trail;

// ==============================
// Dashboard
// ==============================
Breadcrumbs::for('dashboard', fn (Trail $trail) =>
    $trail->push('Dashboard', route('dashboard')));

// ==============================
// Profil
// ==============================
Breadcrumbs::for('profile.edit', fn (Trail $trail) =>
    $trail->parent('dashboard')->push('Profil Saya', route('profile.edit')));

Breadcrumbs::for('profile.password.edit', fn (Trail $trail) =>
    $trail->parent('profile.edit')->push('Ubah Password'));

// ==============================
// Absensi
// ==============================
Breadcrumbs::for('attendances.index', fn (Trail $trail) =>
    $trail->parent('dashboard')->push('Absensi', route('attendances.index')));

Breadcrumbs::for('attendances.create', fn (Trail $trail) =>
    $trail->parent('attendances.index')->push('Absen Sekarang'));

// ==============================
// Cuti / Izin
// ==============================
Breadcrumbs::for('leave-requests.index', fn (Trail $trail) =>
    $trail->parent('dashboard')->push('Cuti / Izin', route('leave-requests.index')));

// ==============================
// Laporan Patroli
// ==============================
Breadcrumbs::for('patrol-reports.index', fn (Trail $trail) =>
    $trail->parent('dashboard')->push('Laporan Patroli', route('patrol-reports.index')));

Breadcrumbs::for('patrol-reports.create', fn (Trail $trail) =>
    $trail->parent('patrol-reports.index')->push('Buat Laporan'));

// ==============================
// Pelaporan (Export)
// ==============================
Breadcrumbs::for('pelaporan.index', fn (Trail $trail) =>
    $trail->parent('dashboard')->push('Pelaporan', route('pelaporan.index')));

// ==============================
// Pos / Lokasi
// ==============================
Breadcrumbs::for('locations.index', fn (Trail $trail) =>
    $trail->parent('dashboard')->push('Pos / Lokasi', route('locations.index')));

Breadcrumbs::for('locations.create', fn (Trail $trail) =>
    $trail->parent('locations.index')->push('Tambah'));

Breadcrumbs::for('locations.edit', fn (Trail $trail, $location) =>
    $trail->parent('locations.index')->push($location->name)->push('Edit'));

Breadcrumbs::for('locations.assignment.edit', fn (Trail $trail, $location) =>
    $trail->parent('locations.index')->push($location->name)->push('Penugasan'));

Breadcrumbs::for('locations.supervisors.edit', fn (Trail $trail, $location) =>
    $trail->parent('locations.index')->push($location->name)->push('Akses Supervisor'));

Breadcrumbs::for('locations.access.show', fn (Trail $trail, $location) =>
    $trail->parent('locations.index')->push($location->name)->push('Akses Pos'));

// ==============================
// Pengguna
// ==============================
Breadcrumbs::for('users.index', fn (Trail $trail) =>
    $trail->parent('dashboard')->push('Pengguna', route('users.index')));

Breadcrumbs::for('users.create', fn (Trail $trail) =>
    $trail->parent('users.index')->push('Tambah'));

Breadcrumbs::for('users.show', fn (Trail $trail, $user) =>
    $trail->parent('users.index')->push($user->name, route('users.show', $user)));

Breadcrumbs::for('users.edit', fn (Trail $trail, $user) =>
    $trail->parent('users.show', $user)->push('Edit'));

Breadcrumbs::for('users.role.edit', fn (Trail $trail, $user) =>
    $trail->parent('users.show', $user)->push('Ubah Role'));

Breadcrumbs::for('users.password.edit', fn (Trail $trail, $user) =>
    $trail->parent('users.show', $user)->push('Ubah Password'));

// ==============================
// Role
// ==============================
Breadcrumbs::for('roles.index', fn (Trail $trail) =>
    $trail->parent('dashboard')->push('Role', route('roles.index')));

Breadcrumbs::for('roles.create', fn (Trail $trail) =>
    $trail->parent('roles.index')->push('Tambah'));

Breadcrumbs::for('roles.edit', fn (Trail $trail, $role) =>
    $trail->parent('roles.index')->push($role->name)->push('Edit'));

Breadcrumbs::for('roles.permissions.edit', fn (Trail $trail, $role) =>
    $trail->parent('roles.index')->push($role->name)->push('Hak Akses'));