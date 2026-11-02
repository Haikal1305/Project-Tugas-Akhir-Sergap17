<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    // Batas maksimal sesi "remember me", dalam menit (8 jam)
    const REMEMBER_MAX_MINUTES = 480;

    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Memproses data login (bisa pakai Email atau Username)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required'],
            'password' => ['required'],
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $dataLogin = [
            $field => $credentials['login'],
            'password' => $credentials['password']
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($dataLogin, $remember)) {
            $request->session()->regenerate();

            // Catat kapan sesi ini mulai, dipakai middleware untuk cek batas 8 jam
            $request->session()->put('login_at', now()->timestamp);
            $request->session()->put('remember_me', $remember);

            if ($remember) {
                // Laravel otomatis membuat cookie "remember_web_..." dengan masa berlaku
                // default 5 tahun. Kita timpa ulang jadi 8 jam saja.
                $guard = Auth::guard();
                $recallerName = $guard->getRecallerName();
                $user = Auth::user();

                if ($user && $user->getRememberToken()) {
                    $recallerValue = $user->getAuthIdentifier() . '|' . $user->getRememberToken() . '|' . $user->getAuthPassword();

                    // Hapus cookie default lalu buat ulang dengan durasi 8 jam
                    Cookie::queue(Cookie::forget($recallerName));
                    Cookie::queue($recallerName, $recallerValue, self::REMEMBER_MAX_MINUTES);
                }
            }

            // Ambil nama user yang baru login, dipakai untuk sapaan di pesan sukses.
            // Fallback ke 'Pengguna' kalau field name kosong/null.
            $name = Auth::user()->name ?? 'Pengguna';

            // Setelah login: kalau user tadinya coba akses halaman tertentu
            // (misal /dashboard/insiden) sebelum diarahkan ke login, dia akan
            // dibawa balik ke sana. Kalau tidak ada, fallback-nya ke dashboard,
            // bukan ke home.
            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang kembali, {$name}!");
        }

        return back()->withErrors([
            'login' => 'Email/Username atau password salah.',
        ])->onlyInput('login');
    }

    // Proses logout
    public function logout(Request $request)
    {
        // Ambil nama SEBELUM logout, karena setelah Auth::logout() user
        // sudah null dan datanya tidak bisa diakses lagi.
        $name = Auth::user()->name ?? 'Pengguna';

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Flash message ditaruh SETELAH invalidate(), karena invalidate()
        // menghapus semua data sesi (termasuk flash message lama). Kalau
        // di-flash sebelum invalidate, pesannya bakal ikut kehapus dan
        // tidak akan muncul di halaman login.
        return redirect('/login')->with('success', "Sampai jumpa lagi, {$name}!");
    }
}