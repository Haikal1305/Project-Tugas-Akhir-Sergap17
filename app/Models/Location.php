<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'address',
    'lat',
    'lng',
    'radius',
])]
class Location extends Model
{
    use HasFactory;

    /**
     * Petugas/Satpam yang ditugaskan ke lokasi ini.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'location_user')
                    ->withPivot('assigned_date', 'shift_start', 'shift_end')
                    ->withTimestamps();
    }

    /**
     * Riwayat absensi yang tercatat di lokasi ini.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Daftar laporan patroli yang masuk dari lokasi ini.
     */
    public function patrolReports(): HasMany
    {
        return $this->hasMany(PatrolReport::class);
    }
}