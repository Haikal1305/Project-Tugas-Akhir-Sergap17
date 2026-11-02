<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pusat Kendali Operasional Keamanan | PT SERGAP 17</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class', // Mengaktifkan dark mode berbasis class
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
    
    {{-- Script Anti-Flicker: Cek tema sebelum render --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        /* =========================================
           NEON GRID - LIGHT MODE (Elegan & Bersih)
           ========================================= */
        body {
            background-color: #f8fafc;
            background-image: 
                /* Garis Grid Vertikal & Horizontal (Warna Emerald Tipis) */
                linear-gradient(to right, rgba(16, 185, 129, 0.08) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(16, 185, 129, 0.08) 1px, transparent 1px);
            background-size: 40px 40px;
            background-attachment: fixed;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* =========================================
           NEON GRID - DARK MODE (Cyberpunk / Tron Vibe)
           ========================================= */
        .dark body {
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
            
            background-size: 
                40px 40px, 
                40px 40px,
                100vw 100vh,
                100vw 100vh;
                
            background-attachment: fixed;
        }

        @keyframes soft-pulse {
            0%, 100% { transform: scale(1);    opacity: 0.45; }
            50%      { transform: scale(1.04); opacity: 0.20; }
        }
        .soft-pulse { animation: soft-pulse 7s ease-in-out infinite; }
        
        ::selection { background: #d1fae5; color: #064e3b; }
        .dark ::selection { background: #059669; color: #ffffff; }
    </style>
</head>
<body class="font-sans antialiased text-slate-900 dark:text-slate-100 min-h-screen flex flex-col transition-colors duration-300">

    {{-- ================= NAVBAR ================= --}}
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg border-b border-slate-200/70 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <a href="/" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-slate-900 dark:bg-brand-600 flex items-center justify-center text-white font-bold text-xs tracking-tight transition-colors duration-300">
                        S17
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="font-semibold text-base text-slate-900 dark:text-white tracking-tight transition-colors duration-300">
                            SERGAP<span class="text-brand-600 dark:text-brand-400">17</span>
                        </span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-medium tracking-wide uppercase transition-colors duration-300">
                            Security Operations
                        </span>
                    </div>
                </a>

                <div class="flex items-center gap-5">
                    <span class="hidden sm:flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider transition-colors duration-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                        System Online
                    </span>

                    {{-- Tombol Switch Tema --}}
                    <button id="theme-toggle" type="button" class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors" title="Ganti Tema">
                        <i id="theme-toggle-icon" class="fas fa-moon text-sm"></i>
                    </button>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-slate-900 dark:bg-brand-600 text-white font-medium text-sm px-4 py-2 rounded-lg hover:bg-slate-800 dark:hover:bg-brand-500 transition-colors">
                                Dashboard <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-slate-900 dark:bg-brand-600 text-white font-medium text-sm px-4 py-2 rounded-lg hover:bg-slate-800 dark:hover:bg-brand-500 transition-colors">
                                Masuk Sistem <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        @endauth
                    @endif
                </div>

            </div>
        </div>
    </nav>

    {{-- ================= HERO ================= --}}
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-20 lg:py-28">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">

                {{-- KIRI --}}
                <div class="lg:col-span-7 space-y-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-brand-100 dark:border-brand-900 bg-brand-50 dark:bg-brand-950/50 text-[11px] text-brand-700 dark:text-brand-400 font-semibold uppercase tracking-wider transition-colors duration-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                        Pusat Kendali Operasional · v.17
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-[1.05] tracking-tight text-slate-900 dark:text-white transition-colors duration-300">
                        Pusat Kendali<br>
                        Operasional<br>
                        <span class="text-brand-600 dark:text-brand-400">Keamanan</span>
                    </h1>

                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-xl leading-relaxed transition-colors duration-300">
                        SERGAP 17 menyatukan pemantauan, pelaporan, dan manajemen pengamanan lapangan dalam satu ruang kendali — real-time, terukur, dan akuntabel.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 bg-slate-900 dark:bg-brand-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-slate-800 dark:hover:bg-brand-500 transition-colors text-sm">
                                <i class="fas fa-shield-halved"></i> Buka Ruang Kendali
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 bg-slate-900 dark:bg-brand-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-slate-800 dark:hover:bg-brand-500 transition-colors text-sm">
                                <i class="fas fa-shield-halved"></i> Masuk ke Sistem
                            </a>
                        @endauth

                        <a href="#fitur" class="inline-flex items-center justify-center gap-2 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold px-6 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-slate-400 dark:hover:border-slate-600 transition-colors text-sm">
                            <i class="fas fa-circle-info text-slate-400 dark:text-slate-500"></i> Pelajari Fitur
                        </a>
                    </div>

                    {{-- Mini stats --}}
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-slate-200 dark:border-slate-800 max-w-md transition-colors duration-300">
                        <div>
                            <div class="text-2xl font-bold text-slate-900 dark:text-white leading-none transition-colors duration-300">3</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2 font-medium transition-colors duration-300">Lapis Otorisasi</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900 dark:text-white leading-none transition-colors duration-300">24/7</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2 font-medium transition-colors duration-300">Pemantauan</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900 dark:text-white leading-none transition-colors duration-300">GPS</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2 font-medium transition-colors duration-300">Validasi Aktif</div>
                        </div>
                    </div>
                </div>

                {{-- KANAN — Visual radar --}}
                <div class="lg:col-span-5 flex justify-center relative">
                    <div class="relative w-full max-w-md aspect-square">

                        {{-- Ring konsentris --}}
                        <div class="absolute inset-0 rounded-full border border-slate-200 dark:border-slate-800 transition-colors duration-300"></div>
                        <div class="absolute inset-10 rounded-full border border-slate-200 dark:border-slate-800 transition-colors duration-300"></div>
                        <div class="absolute inset-20 rounded-full border border-brand-100 dark:border-brand-900/50 transition-colors duration-300"></div>
                        <div class="absolute inset-28 rounded-full border border-dashed border-brand-200 dark:border-brand-800 soft-pulse"></div>

                        {{-- Crosshair ticks --}}
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-px h-3 bg-slate-300 dark:bg-slate-700 transition-colors duration-300"></div>
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-px h-3 bg-slate-300 dark:bg-slate-700 transition-colors duration-300"></div>
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-px w-3 bg-slate-300 dark:bg-slate-700 transition-colors duration-300"></div>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 h-px w-3 bg-slate-300 dark:bg-slate-700 transition-colors duration-300"></div>

                        {{-- Logo di tengah --}}
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="relative">
                                <div class="absolute inset-0 bg-brand-100/60 dark:bg-brand-900/30 blur-2xl rounded-full transition-colors duration-300"></div>
                                <img src="{{ asset('assets/abc.png') }}"
                                     alt="Logo SERGAP 17"
                                     class="relative w-40 sm:w-48 h-auto drop-shadow-md dark:drop-shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                            </div>
                        </div>

                        {{-- Label mengambang --}}
                        <div class="hidden sm:block absolute -top-1 right-2 text-[10px] font-semibold text-brand-700 dark:text-brand-400 bg-white dark:bg-slate-800 border border-brand-100 dark:border-brand-900/50 px-2.5 py-1 rounded-md shadow-sm transition-colors duration-300">
                            GPS · LOCK
                        </div>
                        <div class="hidden sm:block absolute -bottom-1 left-2 text-[10px] font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-2.5 py-1 rounded-md shadow-sm transition-colors duration-300">
                            SHIFT · ACTIVE
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    {{-- ================= FITUR ================= --}}
    <section id="fitur" class="border-t border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-950/60 py-24 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            <div class="max-w-3xl mb-16">
                <div class="text-xs text-brand-600 dark:text-brand-400 font-bold uppercase tracking-wider mb-4 transition-colors duration-300">Arsitektur Otorisasi</div>
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white leading-tight mb-5 transition-colors duration-300">
                    Tiga lapis kendali,<br>
                    satu standar keamanan.
                </h2>
                <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed transition-colors duration-300">
                    Bukan sekadar siapa boleh masuk. SERGAP 17 mengatur siapa boleh apa, atas pos mana, dan dalam kondisi apa — lewat tiga model otorisasi yang saling melengkapi.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-5">

                {{-- 01 · RBAC --}}
                <div class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between mb-8">
                        <div class="text-xs text-slate-400 dark:text-slate-500 font-semibold tracking-wider transition-colors duration-300">01</div>
                        <div class="w-10 h-10 rounded-lg bg-brand-50 dark:bg-brand-950/50 border border-brand-100 dark:border-brand-900/50 flex items-center justify-center text-brand-600 dark:text-brand-400 transition-colors duration-300">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                    <div class="text-[11px] text-brand-600 dark:text-brand-400 font-bold uppercase tracking-wider mb-3 transition-colors duration-300">RBAC</div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 transition-colors duration-300">Kendali Berbasis Peran</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed transition-colors duration-300">
                        Hak akses ditentukan oleh peran — mengelola personel, pos jaga, hingga laporan. Peran menetapkan menu dan aksi apa saja yang bisa diakses secara global.
                    </p>
                </div>

                {{-- 02 · ReBAC --}}
                <div class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between mb-8">
                        <div class="text-xs text-slate-400 dark:text-slate-500 font-semibold tracking-wider transition-colors duration-300">02</div>
                        <div class="w-10 h-10 rounded-lg bg-brand-50 dark:bg-brand-950/50 border border-brand-100 dark:border-brand-900/50 flex items-center justify-center text-brand-600 dark:text-brand-400 transition-colors duration-300">
                            <i class="fas fa-diagram-project"></i>
                        </div>
                    </div>
                    <div class="text-[11px] text-brand-600 dark:text-brand-400 font-bold uppercase tracking-wider mb-3 transition-colors duration-300">ReBAC</div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 transition-colors duration-300">Kendali Berbasis Relasi Pos</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed transition-colors duration-300">
                        Hak kelola tidak berlaku rata di semua pos. Pengguna hanya bisa melihat, mengekspor, atau menugaskan personel pada pos jaga yang menjadi relasi tanggung jawabnya — granular per-lokasi.
                    </p>
                </div>

                {{-- 03 · ABAC --}}
                <div class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between mb-8">
                        <div class="text-xs text-slate-400 dark:text-slate-500 font-semibold tracking-wider transition-colors duration-300">03</div>
                        <div class="w-10 h-10 rounded-lg bg-brand-50 dark:bg-brand-950/50 border border-brand-100 dark:border-brand-900/50 flex items-center justify-center text-brand-600 dark:text-brand-400 transition-colors duration-300">
                            <i class="fas fa-location-crosshairs"></i>
                        </div>
                    </div>
                    <div class="text-[11px] text-brand-600 dark:text-brand-400 font-bold uppercase tracking-wider mb-3 transition-colors duration-300">ABAC</div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 transition-colors duration-300">Kendali Berbasis Konteks</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed transition-colors duration-300">
                        Absensi dan laporan patroli hanya sah bila kondisinya tepat: dalam radius pos jaga, clock-in aktif, dan sesuai jam shift — divalidasi otomatis dari lokasi GPS dan waktu aksi.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= FOOTER ================= --}}
    <footer class="border-t border-slate-200 dark:border-slate-800 py-10 bg-white dark:bg-slate-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-md bg-slate-900 dark:bg-brand-600 flex items-center justify-center text-white font-bold text-[10px] tracking-tight transition-colors duration-300">
                    S17
                </div>
                <span class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-medium transition-colors duration-300">
                    PT SERGAP 17 · Security Operations
                </span>
            </div>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 transition-colors duration-300">
                © {{ date('Y') }} · All systems operational
            </p>
        </div>
    </footer>

    {{-- Script Toggle Tema --}}
    <script>
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

</body>
</html>