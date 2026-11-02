<?php

namespace App\Support;

use App\Models\User;
use Spatie\Permission\Models\Permission;

class NotifiableUsers
{
    /**
     * Ambil semua user yang punya permission tertentu
     * (baik lewat role, maupun permission langsung).
     */
    public static function withPermission(string $permission): \Illuminate\Support\Collection
    {
        if (! Permission::where('name', $permission)->exists()) {
            return collect();
        }

        return User::permission($permission)->get();
    }
}