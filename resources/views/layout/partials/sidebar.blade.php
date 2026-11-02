{{-- Komponen: Sidebar (TailwindAdmin Style - Full Hide) --}}
<aside
    class="fixed inset-y-0 left-0 z-40 w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 flex flex-col transform transition-transform duration-300 ease-in-out"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    {{-- Logo Area --}}
    <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-100 dark:border-slate-800 shrink-0">
        <div class="w-9 h-9 rounded-full bg-brand-500 flex items-center justify-center text-white font-black text-sm shadow-md shadow-brand-500/30 shrink-0">
            S17
        </div>
        <div class="flex flex-col leading-none">
            <span class="font-bold text-lg tracking-tight text-slate-900 dark:text-white">
                SERGAP<span class="text-brand-500">17</span>
            </span>
        </div>
    </div>

    {{-- Navigasi --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-6 space-y-1 px-4">

        {{-- Kategori: HOME --}}
        <p class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-2">ubah</p>

        {{-- Dashboard (Standalone Menu) --}}
        <a href="{{ route('dashboard') }}"
            class="group flex items-center rounded-lg text-sm font-semibold transition-all duration-200 mb-6 px-3 py-2.5 gap-3
            {{ request()->routeIs('dashboard') ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white' }}">
            <i class="fas fa-gauge-high w-5 text-center shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300' }}"></i>
            <span class="whitespace-nowrap">Dashboard</span>
        </a>

        {{-- Kategori: APPS (Menu Operasional) --}}
        @php
            $isOpsActive = request()->routeIs('patrol-reports.*') || request()->routeIs('pelaporan.*') || request()->routeIs('locations.*') || request()->routeIs('attendances.*') || request()->routeIs('leave-requests.*');
        @endphp
        @if(auth()->user()->can('patrol-report view') || auth()->user()->can('patrol-report export') || auth()->user()->can('location view') || auth()->user()->can('attendance view') || auth()->user()->can('leave-request view'))
            <p class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Apps</p>

            <div x-data="{ open: {{ $isOpsActive ? 'true' : 'false' }} }" class="mb-4">
                <button @click="open = !open"
                    class="w-full group flex items-center rounded-lg text-sm font-semibold transition-all duration-200 px-3 py-2.5
                    {{ $isOpsActive ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-layer-group w-5 text-center shrink-0 {{ $isOpsActive ? 'text-white' : 'text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300' }}"></i>
                        <span class="whitespace-nowrap">Operasional</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] ml-auto transition-transform duration-200 {{ $isOpsActive ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    @can('patrol-report view')
                        <a href="{{ route('patrol-reports.index') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium rounded-lg transition-colors {{ request()->routeIs('patrol-reports.*') ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-950/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-500 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <div class="w-1.5 h-1.5 rounded-full border-2 {{ request()->routeIs('patrol-reports.*') ? 'border-brand-500 bg-brand-500' : 'border-slate-400 dark:border-slate-600' }} ml-2 shrink-0"></div>
                            Monitoring Patroli
                        </a>
                    @endcan
                    @can('patrol-report export')
                        <a href="{{ route('pelaporan.index') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium rounded-lg transition-colors {{ request()->routeIs('pelaporan.*') ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-950/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-500 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <div class="w-1.5 h-1.5 rounded-full border-2 {{ request()->routeIs('pelaporan.*') ? 'border-brand-500 bg-brand-500' : 'border-slate-400 dark:border-slate-600' }} ml-2 shrink-0"></div>
                            Pelaporan
                        </a>
                    @endcan
                    @can('location view')
                        <a href="{{ route('locations.index') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium rounded-lg transition-colors {{ request()->routeIs('locations.*') ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-950/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-500 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <div class="w-1.5 h-1.5 rounded-full border-2 {{ request()->routeIs('locations.*') ? 'border-brand-500 bg-brand-500' : 'border-slate-400 dark:border-slate-600' }} ml-2 shrink-0"></div>
                            Kontrol Lokasi
                        </a>
                    @endcan
                    @can('attendance view')
                        <a href="{{ route('attendances.index') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium rounded-lg transition-colors {{ request()->routeIs('attendances.*') ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-950/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-500 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <div class="w-1.5 h-1.5 rounded-full border-2 {{ request()->routeIs('attendances.*') ? 'border-brand-500 bg-brand-500' : 'border-slate-400 dark:border-slate-600' }} ml-2 shrink-0"></div>
                            Absensi
                        </a>
                    @endcan
                    @can('leave-request view')
                        <a href="{{ route('leave-requests.index') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium rounded-lg transition-colors {{ request()->routeIs('leave-requests.*') ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-950/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-500 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <div class="w-1.5 h-1.5 rounded-full border-2 {{ request()->routeIs('leave-requests.*') ? 'border-brand-500 bg-brand-500' : 'border-slate-400 dark:border-slate-600' }} ml-2 shrink-0"></div>
                            Pengajuan Izin
                        </a>
                    @endcan
                </div>
            </div>
        @endif

        {{-- Kategori: PAGES (Pengaturan) --}}
        @php
            $isSettingsActive = request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('profile.*');
        @endphp
        @if(auth()->user()->can('user view') || auth()->user()->can('role view') || Route::has('profile.edit'))
            <p class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Pages</p>

            <div x-data="{ open: {{ $isSettingsActive ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full group flex items-center rounded-lg text-sm font-semibold transition-all duration-200 px-3 py-2.5
                    {{ $isSettingsActive ? 'bg-brand-500 text-white shadow-md shadow-brand-500/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-cog w-5 text-center shrink-0 {{ $isSettingsActive ? 'text-white' : 'text-slate-400 group-hover:text-slate-600 dark:text-slate-500 dark:group-hover:text-slate-300' }}"></i>
                        <span class="whitespace-nowrap">Pengaturan</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] ml-auto transition-transform duration-200 {{ $isSettingsActive ? 'text-white' : 'text-slate-400 dark:text-slate-500' }}" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" x-collapse class="mt-1 space-y-1">
                    @can('user view')
                        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-950/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-500 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <div class="w-1.5 h-1.5 rounded-full border-2 {{ request()->routeIs('users.*') ? 'border-brand-500 bg-brand-500' : 'border-slate-400 dark:border-slate-600' }} ml-2 shrink-0"></div>
                            Data Personil
                        </a>
                    @endcan
                    @can('role view')
                        <a href="{{ route('roles.index') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium rounded-lg transition-colors {{ request()->routeIs('roles.*') ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-950/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-500 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <div class="w-1.5 h-1.5 rounded-full border-2 {{ request()->routeIs('roles.*') ? 'border-brand-500 bg-brand-500' : 'border-slate-400 dark:border-slate-600' }} ml-2 shrink-0"></div>
                            Manajemen Role
                        </a>
                    @endcan
                    @if(Route::has('profile.edit'))
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-[13px] font-medium rounded-lg transition-colors {{ request()->routeIs('profile.*') ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-950/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-500 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <div class="w-1.5 h-1.5 rounded-full border-2 {{ request()->routeIs('profile.*') ? 'border-brand-500 bg-brand-500' : 'border-slate-400 dark:border-slate-600' }} ml-2 shrink-0"></div>
                            Pengaturan Akun
                        </a>
                    @endif
                </div>
            </div>
        @endif

    </nav>

    {{-- Footer Sidebar (Avatar) --}}
    <div class="p-4 border-t border-slate-100 dark:border-slate-800 shrink-0 overflow-hidden bg-white dark:bg-slate-900">
        <div class="flex items-center gap-3 px-2">
            <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold text-sm shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
                @if (auth()->user()->profile)
                    <img src="{{ asset('storage/' . auth()->user()->profile) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email ?? '-' }}</p>
            </div>
        </div>
    </div>
</aside>