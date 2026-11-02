<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'username', 'email', 'password', 'profile'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Pos jaga / Lokasi penugasan yang dimiliki oleh user (satpam).
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_user')
                    ->withPivot('assigned_date', 'shift_start', 'shift_end')
                    ->withTimestamps();
    }

    /**
     * Riwayat absensi milik user (satpam) ini.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Pengajuan izin milik user ini (sebagai yang mengajukan izin).
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'user_id');
    }

    /**
     * Pengajuan izin yang di-review (approve/reject) oleh user ini (sebagai admin/supervisor).
     */
    public function reviewedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'reviewed_by');
    }

    /**
     * Riwayat laporan patroli yang dibuat oleh user (satpam) ini.
     */
    public function patrolReports(): HasMany
    {
        return $this->hasMany(PatrolReport::class);
    }
}