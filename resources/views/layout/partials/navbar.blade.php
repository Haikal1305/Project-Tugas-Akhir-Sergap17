{{-- Komponen: Navbar --}}
<header class="sticky top-0 z-30 h-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/70 dark:border-slate-800 flex items-center px-4 sm:px-6 lg:px-8 gap-4 transition-colors duration-300"
    x-data="notificationCenter({{ auth()->user()->unreadNotifications->count() }}, {{ auth()->user()->notifications->take(10)->toJson() }}, {{ auth()->id() }})">

    {{-- Tombol Buka/Tutup Sidebar --}}
    <button @click="sidebarOpen = !sidebarOpen"
        class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 hover:text-brand-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-brand-400 transition-all shrink-0"
        title="Sembunyikan/Tampilkan Sidebar">
        <i class="fas fa-bars text-lg"></i>
    </button>

    {{-- Judul Halaman --}}
    <div class="min-w-0 ml-2">
        <h1 class="text-lg font-bold text-slate-900 dark:text-white truncate tracking-tight">
            @yield('pageTitle', 'Dashboard')
        </h1>
    </div>

    <div class="flex-1"></div>

    {{-- Aksi Kanan --}}
    <div class="flex items-center gap-2 sm:gap-3">

        {{-- Tombol Switch Tema --}}
        <button id="theme-toggle" type="button"
            class="w-10 h-10 flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors shrink-0"
            title="Ganti Tema">
            <i id="theme-toggle-icon" class="fas fa-moon text-lg"></i>
        </button>

        {{-- Notifikasi --}}
        <div class="relative">
            <button @click="notifOpen = !notifOpen" @click.outside="notifOpen = false"
                class="relative w-10 h-10 flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors">
                <i class="fas fa-bell text-lg"></i>
                <span x-show="unreadCount > 0" x-cloak
                    class="absolute top-2 right-2.5 w-2 h-2 rounded-full bg-brand-500 ring-2 ring-white dark:ring-slate-900"></span>
            </button>

            <div x-show="notifOpen" x-transition x-cloak
                class="absolute right-0 mt-3 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-40 max-h-[28rem] overflow-y-auto">

                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <p class="text-sm font-bold text-slate-900 dark:text-white">Notifikasi</p>
                    <button x-show="unreadCount > 0" @click="markAllAsRead()"
                        class="text-[11px] font-bold text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300 transition-colors">
                        Tandai dibaca
                    </button>
                </div>

                <template x-if="notifications.length === 0">
                    <p class="px-4 py-8 text-sm text-slate-400 dark:text-slate-500 text-center font-medium">Belum ada notifikasi</p>
                </template>

                <template x-for="notif in notifications" :key="notif.id">
                    <div class="flex items-start gap-1 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-50 dark:border-slate-800/50 last:border-0"
                        :class="notif.read_at ? 'opacity-60' : ''">
                        <a href="#" @click.prevent="goTo(notif)" class="flex items-start gap-3 min-w-0 flex-1">
                            <span class="w-2 h-2 rounded-full mt-1.5 shrink-0"
                                :class="{
                                    'bg-blue-500': notif.data.type === 'info',
                                    'bg-amber-500': notif.data.type === 'warning',
                                    'bg-brand-500': notif.data.type === 'success',
                                    'bg-red-500': notif.data.type === 'danger'
                                }"></span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate" x-text="notif.data.title"></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2" x-text="notif.data.message"></p>
                            </div>
                        </a>
                        <button @click="deleteNotification(notif)"
                            class="shrink-0 w-6 h-6 flex items-center justify-center rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 dark:text-slate-600 dark:hover:text-red-400 dark:hover:bg-red-950/30 transition-colors mt-0.5"
                            aria-label="Hapus notifikasi">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Dropdown User --}}
        <div x-data="{ userMenu: false }" class="relative">
            <button @click="userMenu = !userMenu" @click.outside="userMenu = false"
                class="flex items-center gap-2.5 focus:outline-none">
                <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold text-sm shrink-0 overflow-hidden shadow-sm">
                    @if (auth()->user()->profile)
                        <img src="{{ asset('storage/' . auth()->user()->profile) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    @endif
                </div>
                <i class="fas fa-chevron-down text-[10px] text-slate-400 dark:text-slate-500 hidden sm:block"></i>
            </button>

            <div x-show="userMenu" x-transition x-cloak
                class="absolute right-0 mt-3 w-72 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 py-0 overflow-hidden z-40">
                <div class="bg-slate-50/80 dark:bg-slate-800/50 px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div class="min-w-0 pr-2">
                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate mt-0.5">{{ auth()->user()->email ?? '-' }}</p>
                    </div>
                    <button @click="userMenu = false" class="text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-2 space-y-0.5">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition-colors">
                        <i class="fas fa-user w-4 text-center text-slate-400 dark:text-slate-500"></i> Profil Saya
                    </a>
                    <a href="{{ route('profile.password.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition-colors">
                        <i class="fas fa-key w-4 text-center text-slate-400 dark:text-slate-500"></i> Ganti Password
                    </a>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-800 p-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30 transition-colors">
                            <i class="fas fa-sign-out-alt w-4 text-center"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function notificationCenter(initialUnreadCount, initialNotifications, userId) {
        return {
            notifOpen: false,
            unreadCount: initialUnreadCount,
            notifications: initialNotifications,

            init() {
                if (!window.Echo) return;
                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        this.notifications.unshift({ id: notification.id ?? crypto.randomUUID(), data: notification, read_at: null });
                        this.unreadCount++;
                    });
            },
            goTo(notif) {
                this.markAsRead(notif);
                window.location.href = notif.data.url ?? '#';
            },
            markAsRead(notif) {
                if (notif.read_at) return;
                notif.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                fetch(`/notifications/${notif.id}/read`, {
                    method: 'POST', keepalive: true, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                }).catch(err => console.error(err));
            },
            deleteNotification(notif) {
                const wasUnread = !notif.read_at;
                this.notifications = this.notifications.filter(n => n.id !== notif.id);
                if (wasUnread) this.unreadCount = Math.max(0, this.unreadCount - 1);
                fetch(`/notifications/${notif.id}`, {
                    method: 'DELETE', keepalive: true, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                }).catch(err => console.error(err));
            },
            markAllAsRead() {
                this.notifications.forEach(n => n.read_at = n.read_at ?? new Date().toISOString());
                this.unreadCount = 0;
                fetch('/notifications/read-all', {
                    method: 'POST', keepalive: true, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                }).catch(err => console.error(err));
            },
        }
    }
</script>