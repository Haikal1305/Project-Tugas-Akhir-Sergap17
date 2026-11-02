<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSessionExpiry
{
    const MAX_MINUTES = 480; // 8 jam

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && $request->session()->has('login_at')) {
            $loginAt = $request->session()->get('login_at');
            $elapsedMinutes = now()->diffInMinutes(\Carbon\Carbon::createFromTimestamp($loginAt));

            if ($elapsedMinutes >= self::MAX_MINUTES) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->withErrors([
                    'login' => 'Sesi Anda telah berakhir setelah 8 jam. Silakan masuk kembali.',
                ]);
            }
        }

        return $next($request);
    }
}