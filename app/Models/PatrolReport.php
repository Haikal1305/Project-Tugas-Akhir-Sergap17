<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'location_id',
    'date',
    'patrol_at',
    'report',
    'status',
    'photo',
    'lat',
    'lng',
])]
class PatrolReport extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'patrol_at' => 'datetime',
            'lat' => 'decimal:8',
            'lng' => 'decimal:8',
        ];
    }

    /**
     * Petugas (Satpam) yang membuat laporan patroli.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pos jaga / Lokasi tempat laporan patroli ini dibuat.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}