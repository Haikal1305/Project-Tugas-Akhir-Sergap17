<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_id',
        'date',
        'clock_in_at',
        'clock_out_at',
        'status',
        'clock_in_lat',
        'clock_in_lng',
        'clock_in_photo',
        'clock_in_address',
        'clock_out_lat',
        'clock_out_lng',
        'clock_out_photo',
        'clock_out_address',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
        'clock_in_lat' => 'decimal:7',
        'clock_in_lng' => 'decimal:7',
        'clock_out_lat' => 'decimal:7',
        'clock_out_lng' => 'decimal:7',
    ];

    /**
     * User (Satpam) yang melakukan absensi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lokasi/Pos tempat absensi ini terikat.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}