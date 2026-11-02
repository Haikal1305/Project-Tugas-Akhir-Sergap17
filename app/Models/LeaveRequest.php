<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'date', 'reason', 'attachment', 'status', 'submitted_by', 'reviewed_by', 'reviewed_at', 'admin_note'])]
class LeaveRequest extends Model
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
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * Personel yang mengajukan izin.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * User yang menginput pengajuan ini (bisa personel itu sendiri, bisa admin atas namanya).
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Admin/supervisor yang mereview (approve/reject) pengajuan ini.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Menandai apakah pengajuan ini "mendadak" (diajukan pada tanggal izin itu sendiri
     * atau bahkan setelah tanggal itu lewat). Bukan untuk memblokir, hanya penanda
     * kewaspadaan bagi admin saat review.
     */
    public function isLastMinute(): bool
    {
        return $this->created_at->toDateString() >= $this->date->toDateString();
    }
}