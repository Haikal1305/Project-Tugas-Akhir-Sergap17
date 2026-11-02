<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PatrolReportController;
use App\Http\Controllers\PatrolReportExportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;


// Route Tamu (Guest) - Hanya bisa diakses jika belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// Halaman Utama
Route::get('/', function () {
    return view('home');
})->name('home');

// Route yang diproteksi (Harus Login dulu)
Route::middleware(['auth', 'session.expiry'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==============================
    // Manajemen Profil Pengguna (Mandiri)
    // ==============================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'editPassword'])->name('password.edit');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // ==============================
    // Notifikasi (Bell Icon Navbar websocket (Reverb))
    // ==============================
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // ==============================
    // Manajemen Absensi (Attendance)
    // ==============================
    Route::prefix('attendances')->name('attendances.')->group(function () {
        Route::middleware('permission:attendance view')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
        });

        Route::middleware('permission:attendance create')->group(function () {
            Route::get('/create', [AttendanceController::class, 'create'])->name('create');
            Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
            Route::post('/{attendance}/clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');
        });

        Route::middleware('permission:attendance delete')->group(function () {
            Route::delete('/{attendance}', [AttendanceController::class, 'destroy'])->name('destroy');
        });
    });

    // ==============================
    // Manajemen Cuti / Izin (Leave Request)
    // ==============================
    Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
        Route::middleware('permission:leave-request view')->group(function () {
            Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
        });

        Route::middleware('permission:leave-request create')->group(function () {
            Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
        });

        Route::middleware('permission:leave-request review')->group(function () {
            Route::patch('/{leaveRequest}/review', [LeaveRequestController::class, 'review'])->name('review');
        });

        Route::middleware('permission:leave-request delete')->group(function () {
            Route::delete('/{leaveRequest}', [LeaveRequestController::class, 'destroy'])->name('destroy');
        });
    });

    // ==============================
    // Manajemen Monitoring Patroli (Patrol Report)
    // ==============================
    Route::prefix('patrol-reports')->name('patrol-reports.')->group(function () {
        Route::middleware('permission:patrol-report view')->group(function () {
            Route::get('/', [PatrolReportController::class, 'index'])->name('index');
        });

        Route::middleware('permission:patrol-report create')->group(function () {
            Route::get('/create', [PatrolReportController::class, 'create'])->name('create');
            Route::post('/', [PatrolReportController::class, 'store'])->name('store');
        });

        Route::middleware('permission:patrol-report delete')->group(function () {
            Route::delete('/{patrolReport}', [PatrolReportController::class, 'destroy'])->name('destroy');
        });

        // Catatan: hak kelola akses per-pos (view/export) untuk laporan patroli
        // TIDAK lagi punya route/halaman sendiri di sini. Sejak guard Casbin
        // 'patrol' dihapus, view/export laporan patroli mengikuti relasi ReBAC
        // milik Location (guard 'basic') — dikelola lewat locations.supervisors.edit/update
        // di bawah, action 'export' sudah termasuk di $managedActions LocationController.
    });

    // ==============================
    // Pelaporan (Export PDF/Excel Laporan Patroli)
    // Route ini tetap pakai permission Spatie 'patrol-report export' sebagai gerbang
    // global (siapa yang boleh mengakses fitur export sama sekali). Otorisasi
    // granular per-pos ('siapa boleh export laporan pos X') dicek di controller
    // lewat Enforcer::guard('basic') terhadap relasi lokasi, bukan di sini.
    // ==============================
    Route::prefix('pelaporan')->name('pelaporan.')->group(function () {
        Route::middleware('permission:patrol-report export')->group(function () {
            Route::get('/', [PatrolReportExportController::class, 'index'])->name('index');
            Route::get('/export/excel', [PatrolReportExportController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [PatrolReportExportController::class, 'exportPdf'])->name('export.pdf');
        });
    });

    // ==============================
    // Manajemen Pos / Lokasi (Location)
    // ==============================
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::middleware('permission:location create')->group(function () {
            Route::get('/create', [LocationController::class, 'create'])->name('create');
            Route::post('/', [LocationController::class, 'store'])->name('store');
        });

        Route::middleware('permission:location assign')->group(function () {
            Route::get('/{location}/assignment', [LocationController::class, 'editAssignment'])->name('assignment.edit');
            Route::put('/{location}/assignment', [LocationController::class, 'updateAssignment'])->name('assignment.update');
        });

        Route::middleware('permission:location manage-all')->group(function () {
            Route::get('/{location}/supervisors', [LocationController::class, 'editSupervisors'])->name('supervisors.edit');
            Route::put('/{location}/supervisors', [LocationController::class, 'updateSupervisors'])->name('supervisors.update');
        });

        Route::middleware('permission:location update')->group(function () {
            Route::get('/{location}/edit', [LocationController::class, 'edit'])->name('edit');
            Route::put('/{location}', [LocationController::class, 'update'])->name('update');
        });

        Route::middleware('permission:location delete')->group(function () {
            Route::delete('/{location}', [LocationController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('permission:location view')->group(function () {
            Route::get('/', [LocationController::class, 'index'])->name('index');
            Route::get('/{location}/access', [LocationController::class, 'showAccess'])->name('access.show');
        });
    });

    // ==============================
    // Manajemen User (RBAC Global)
    // ==============================
    Route::prefix('users')->name('users.')->group(function () {
        Route::middleware('permission:user create')->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
        });

        Route::middleware('permission:user update role')->group(function () {
            Route::get('/{user}/role', [UserController::class, 'editRole'])->name('role.edit');
            Route::put('/{user}/role', [UserController::class, 'updateRole'])->name('role.update');
        });

        Route::middleware('permission:user update')->group(function () {
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
        });

        Route::middleware('permission:user update password')->group(function () {
            Route::get('/{user}/password', [UserController::class, 'editPassword'])->name('password.edit');
            Route::put('/{user}/password', [UserController::class, 'updatePassword'])->name('password.update');
        });

        Route::middleware('permission:user delete')->group(function () {
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('permission:user view')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
        });
    });

    // ==============================
    // Manajemen Role (RBAC)
    // ==============================
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::middleware('permission:role create')->group(function () {
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
        });

        Route::middleware('permission:role update permission')->group(function () {
            Route::get('/{role}/permissions', [RolePermissionController::class, 'edit'])->name('permissions.edit');
            Route::put('/{role}/permissions', [RolePermissionController::class, 'update'])->name('permissions.update');
        });

        Route::middleware('permission:role update')->group(function () {
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        });

        Route::middleware('permission:role delete')->group(function () {
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('permission:role view')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
        });
    });
});
