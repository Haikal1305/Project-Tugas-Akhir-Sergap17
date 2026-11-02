<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: window.matchMedia('(min-width: 1024px)').matches }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('pageTitle', 'Dashboard') | PT SERGAP 17</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
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

    {{-- Script Anti-Flicker: cek tema sebelum render --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        ::selection {
            background: #d1fae5;
            color: #064e3b;
        }
        .dark ::selection {
            background: #10b981;
            color: #ffffff;
        }

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
    </style>

    {{-- Laravel Echo + Pusher client (Reverb). Harus dimuat SEBELUM Alpine --}}
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
        try {
            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: @json(config('broadcasting.connections.reverb.key')),
                wsHost: window.location.hostname,
                wsPort: window.location.port || 80,
                wssPort: window.location.port || 443,
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                },
            });
        } catch (err) {
            console.error('Gagal inisialisasi Echo:', err);
        }
    </script>

    {{-- Alpine.js: plugin collapse HARUS sebelum core, versi disamakan --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.13.5/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>

<body class="font-sans antialiased text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-300">

    {{-- Sidebar Component --}}
    @include('layout.partials.sidebar')

    {{-- Overlay Gelap (Hanya muncul di Mobile saat sidebar dibuka) --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" x-transition.opacity
        class="fixed inset-0 z-30 bg-slate-900/40 backdrop-blur-sm lg:hidden"></div>

    {{-- Area Konten Utama --}}
    <div class="flex flex-col min-h-screen transition-[padding] duration-300 ease-in-out"
        :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-0'">

        {{-- Navbar Component --}}
        @include('layout.partials.navbar')

        {{-- Konten Halaman --}}
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
            @if (Breadcrumbs::exists())
                {{ Breadcrumbs::render() }}
            @endif

            @yield('content')
        </main>

        {{-- Footer Component --}}
        @include('layout.partials.footer')

    </div>

    @stack('scripts')

    {{-- Script Toggle Tema --}}
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleIcon = document.getElementById('theme-toggle-icon');
        const htmlEl = document.documentElement;

        function updateThemeIcon() {
            if (!themeToggleIcon) return;
            if (htmlEl.classList.contains('dark')) {
                themeToggleIcon.classList.remove('fa-moon');
                themeToggleIcon.classList.add('fa-sun');
            } else {
                themeToggleIcon.classList.remove('fa-sun');
                themeToggleIcon.classList.add('fa-moon');
            }
        }

        updateThemeIcon();

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                htmlEl.classList.toggle('dark');
                localStorage.setItem('theme', htmlEl.classList.contains('dark') ? 'dark' : 'light');
                updateThemeIcon();
            });
        }
    </script>

    {{-- Konfigurasi SweetAlert (menyesuaikan tema) --}}
    <script>
        const sergapTheme = {
            background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a',
        };

        const sergapToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            ...sergapTheme,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
    </script>

    {{-- Notifikasi Flash Message --}}
    @if (session('success'))
        <script>
            sergapToast.fire({
                icon: 'success',
                title: @json(session('success'))
            });
        </script>
    @endif

    @if (session('info'))
        <script>
            sergapToast.fire({
                icon: 'info',
                title: @json(session('info'))
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                ...sergapTheme,
                icon: 'error',
                title: 'Gagal',
                text: @json(session('error')),
                confirmButtonColor: '#059669',
                confirmButtonText: 'Mengerti'
            });
        </script>
    @endif

    @if (session('warning'))
        <script>
            Swal.fire({
                ...sergapTheme,
                icon: 'warning',
                title: 'Perhatian',
                text: @json(session('warning')),
                confirmButtonColor: '#059669',
                confirmButtonText: 'Mengerti'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                ...sergapTheme,
                icon: 'error',
                title: 'Periksa Kembali',
                html: @json(implode('<br>', $errors->all())),
                confirmButtonColor: '#059669',
                confirmButtonText: 'Mengerti'
            });
        </script>
    @endif

</body>

</html>