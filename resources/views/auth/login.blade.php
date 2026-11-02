<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in | Pusat Kendali Operasional PT SERGAP 17</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Tambahkan darkMode: 'class' agar bisa di-toggle manual
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#ecfdf5',
                            100: '#d1fae5',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    }
                }
            }
        }
    </script>

    {{-- SweetAlert2: untuk notifikasi flash message (logout, dll) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        ::selection { background: #d1fae5; color: #064e3b; }
        .dark ::selection { background: #059669; color: #ffffff; }
        
        /* =========================================
           NEON GRID - LIGHT MODE (Elegan & Bersih)
           ========================================= */
        .neon-grid-bg {
            background-color: #f8fafc;
            background-image: 
                /* Garis Grid Vertikal & Horizontal (Warna Emerald Tipis) */
                linear-gradient(to right, rgba(16, 185, 129, 0.08) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(16, 185, 129, 0.08) 1px, transparent 1px);
            background-size: 40px 40px;
            background-attachment: fixed;
        }

        /* =========================================
           NEON GRID - DARK MODE (Cyberpunk / Tron Vibe)
           ========================================= */
        .dark .neon-grid-bg {
            background-color: #020617; /* Warna dasar sangat gelap (Slate-950) */
            background-image: 
                /* 1. Garis Grid Vertikal (Neon Emerald) */
                linear-gradient(to right, rgba(16, 185, 129, 0.15) 1px, transparent 1px),
                /* 2. Garis Grid Horizontal (Neon Emerald) */
                linear-gradient(to bottom, rgba(16, 185, 129, 0.15) 1px, transparent 1px),
                /* 3. Pendaran Cahaya Neon Hijau (Kiri Atas) */
                radial-gradient(circle at 0% 0%, rgba(16, 185, 129, 0.25) 0%, transparent 50%),
                /* 4. Pendaran Cahaya Neon Biru/Cyan (Kanan Bawah) */
                radial-gradient(circle at 100% 100%, rgba(14, 165, 233, 0.2) 0%, transparent 50%);
            background-size: 40px 40px, 40px 40px, 100vw 100vh, 100vw 100vh;
            background-attachment: fixed;
        }

        /* Transisi halus saat ganti tema */
        body, aside, main, input, button, a, div {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>

    {{-- Script Anti-Flicker: Cek tema sebelum render untuk mencegah layar berkedip putih --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="font-sans antialiased text-slate-900 dark:text-slate-100 bg-white dark:bg-slate-900">

    <div class="flex min-h-screen">

        {{-- ================= KIRI: Branding (Neon GTI Background) ================= --}}
        <div class="hidden lg:flex lg:w-1/2 relative neon-grid-bg border-r border-slate-200/70 dark:border-slate-800 items-center justify-center overflow-hidden">

            {{-- Center emerald glow (Tambahan efek pendaran lembut di tengah) --}}
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-brand-100/40 dark:bg-brand-900/10 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Radar rings konsentris --}}
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[520px] h-[520px] rounded-full border border-slate-200/80 dark:border-slate-800/80"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] rounded-full border border-slate-200/80 dark:border-slate-800/80"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[280px] h-[280px] rounded-full border border-brand-100 dark:border-brand-900/50"></div>

            <div class="relative z-10 px-12 xl:px-20 py-16 flex flex-col justify-between w-full min-h-screen">

                {{-- Top: mini logo --}}
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-slate-900 dark:bg-brand-600 flex items-center justify-center text-white font-bold text-xs tracking-tight">
                        S17
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="font-semibold text-base text-slate-900 dark:text-white tracking-tight">
                            SERGAP<span class="text-brand-600 dark:text-brand-400">17</span>
                        </span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-medium tracking-wide uppercase">
                            Security Operations
                        </span>
                    </div>
                </div>

                {{-- Center: logo + headline --}}
                <div class="text-center my-8">
                    <div class="relative inline-block mb-8">
                        <div class="absolute inset-0 bg-brand-100/60 dark:bg-brand-900/30 blur-2xl rounded-full"></div>
                        <img src="{{ asset('assets/abc.png') }}"
                             alt="Logo SERGAP 17"
                             class="relative w-36 xl:w-44 h-auto mx-auto drop-shadow-md dark:drop-shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                    </div>

                    <h1 class="text-3xl xl:text-4xl font-bold tracking-tight mb-4 leading-tight text-slate-900 dark:text-white">
                        Pusat Kendali<br>
                        Operasional <span class="text-brand-600 dark:text-brand-400">Keamanan</span>
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 text-base max-w-sm mx-auto leading-relaxed">
                        Sistem terintegrasi pemantauan dan manajemen pengamanan operasional PT SERGAP 17.
                    </p>

                    <div class="mt-10 inline-flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 px-4 py-2.5 rounded-full border border-slate-200 dark:border-slate-700 shadow-sm">
                        <i class="fas fa-shield-halved text-brand-600 dark:text-brand-500"></i>
                        Akses Ruang Kendali Terenkripsi
                    </div>
                </div>

                {{-- Bottom: status + version --}}
                <div class="flex items-center gap-3 text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-medium">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 dark:bg-brand-400"></span>
                        System Online
                    </span>
                    <span class="text-slate-300 dark:text-slate-600">·</span>
                    <span>v.17</span>
                </div>
            </div>
        </div>

        {{-- ================= KANAN: Form Login ================= --}}
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 sm:px-12 xl:px-20 bg-white dark:bg-slate-900 relative">

            {{-- Top Actions (Theme Toggle & Kembali) --}}
            <div class="absolute top-8 right-8 sm:right-12 xl:right-20 flex items-center gap-4">
                
                {{-- Tombol Switch Tema --}}
                <button id="theme-toggle" type="button" class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors" title="Ganti Tema">
                    <i id="theme-toggle-icon" class="fas fa-moon text-sm"></i>
                </button>

                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <i class="fas fa-arrow-left text-[10px]"></i> Kembali
                </a>
            </div>

            <div class="max-w-md w-full mx-auto">

                {{-- Mobile-only logo --}}
                <div class="flex lg:hidden items-center gap-3 mb-10">
                    <div class="w-9 h-9 rounded-lg bg-slate-900 dark:bg-brand-600 flex items-center justify-center text-white font-bold text-xs tracking-tight">S17</div>
                    <div class="flex flex-col leading-none">
                        <span class="font-semibold text-base text-slate-900 dark:text-white tracking-tight">SERGAP<span class="text-brand-600 dark:text-brand-400">17</span></span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-medium tracking-wide uppercase">Security Operations</span>
                    </div>
                </div>

                <div class="mb-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-brand-100 dark:border-brand-900 bg-brand-50 dark:bg-brand-950/50 text-[10px] font-bold uppercase tracking-wider text-brand-700 dark:text-brand-400 mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                        Portal Autentikasi
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">Selamat datang kembali</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">Masukkan Email / Username dan kata sandi Anda untuk mengakses ruang kendali.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900/50 flex items-start gap-3 text-red-700 dark:text-red-400">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                        <ul class="text-sm font-medium space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.process') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="login" class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">Email / Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <i class="fas fa-user-shield text-sm"></i>
                            </div>
                            <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus
                                autocomplete="username"
                                placeholder="nama@sergap17.com atau username"
                                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 focus:bg-white dark:focus:bg-slate-800/80 block w-full pl-11 p-3.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Kata Sandi</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors">
                                    Lupa sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 focus:bg-white dark:focus:bg-slate-800/80 block w-full pl-11 pr-11 p-3.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" />

                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-slate-400 dark:text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors" id="togglePassword">
                                <i class="fas fa-eye text-sm" id="eyeIcon"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="remember" type="checkbox" name="remember"
                            class="w-4 h-4 text-brand-600 bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-brand-500 focus:ring-2 cursor-pointer">
                        <label for="remember" class="ml-2 text-sm font-medium text-slate-600 dark:text-slate-400 cursor-pointer">Ingat sesi saya</label>
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-lg text-sm font-semibold text-white bg-slate-900 dark:bg-brand-600 hover:bg-slate-800 dark:hover:bg-brand-500 focus:outline-none focus:ring-4 focus:ring-slate-900/10 dark:focus:ring-brand-500/30 transition-colors">
                        Masuk ke Sistem <i class="fas fa-arrow-right text-xs"></i>
                    </button>

                </form>

                <p class="text-center text-xs text-slate-400 dark:text-slate-500 mt-10 leading-relaxed">
                    Dengan masuk, Anda menyetujui kebijakan keamanan PT SERGAP 17.
                </p>
            </div>

            <div class="absolute bottom-8 left-0 right-0 text-center lg:text-right lg:pr-12 xl:pr-20 px-6">
                <p class="text-xs text-slate-400 dark:text-slate-500">
                    &copy; {{ date('Y') }} PT SERGAP 17 · All rights reserved
                </p>
            </div>

        </div>
    </div>

    <script>
        // --- Script Toggle Password ---
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });

        // --- Script Toggle Tema (Dark/Light) ---
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleIcon = document.getElementById('theme-toggle-icon');
        const html = document.documentElement;

        // Fungsi untuk update ikon
        function updateIcon() {
            if (html.classList.contains('dark')) {
                themeToggleIcon.classList.remove('fa-moon');
                themeToggleIcon.classList.add('fa-sun');
            } else {
                themeToggleIcon.classList.remove('fa-sun');
                themeToggleIcon.classList.add('fa-moon');
            }
        }

        // Set ikon saat halaman pertama kali dimuat
        updateIcon();

        // Event listener tombol
        themeToggleBtn.addEventListener('click', function() {
            html.classList.toggle('dark');
            
            if (html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
            updateIcon();
        });
    </script>

    {{-- Notifikasi flash message via SweetAlert2 --}}
    @if (session('success'))
        <script>
            // Cek tema untuk SweetAlert
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: isDark ? '#1e293b' : '#ffffff',
                color: isDark ? '#f8fafc' : '#0f172a'
            });
        </script>
    @endif

    @if (session('info'))
        <script>
            const isDarkInfo = document.documentElement.classList.contains('dark');
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: @json(session('info')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: isDarkInfo ? '#1e293b' : '#ffffff',
                color: isDarkInfo ? '#f8fafc' : '#0f172a'
            });
        </script>
    @endif

</body>
</html>