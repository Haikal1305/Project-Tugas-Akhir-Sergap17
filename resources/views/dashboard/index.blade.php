@extends('layout.app')

@section('pageTitle', 'Dashboard')

@section('content')

    <div class="space-y-6 mb-8">

        {{-- 1. Hero Banner (Modern & Clean) --}}
        <div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-10 overflow-hidden shadow-sm flex flex-col md:flex-row items-center justify-between gap-8 transition-colors duration-300">
            {{-- Aksen Background Belakang --}}
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-50 dark:bg-brand-900/20 rounded-full blur-3xl pointer-events-none transition-colors duration-300"></div>
            <div class="absolute bottom-0 right-40 w-40 h-40 bg-blue-50 dark:bg-blue-900/20 rounded-full blur-2xl pointer-events-none transition-colors duration-300"></div>

            <div class="relative z-10 w-full md:w-2/3">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold uppercase tracking-wide mb-4 transition-colors duration-300">
                    <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                    Pusat Kendali Operasional Keamanan
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white mb-3 leading-tight tracking-tight transition-colors duration-300">
                    Selamat Datang, <br>
                    <span class="text-brand-600 dark:text-brand-400">{{ auth()->user()->name ?? 'Pengguna' }}</span>
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm sm:text-base max-w-lg leading-relaxed mb-6 transition-colors duration-300">
                    Pantau kondisi seluruh pos jaga hari ini. Cek personel yang sedang bertugas dan tinjau laporan patroli secara real-time.
                </p>
                
                @can('patrol-report create')
                <a href="{{ route('patrol-reports.create') }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white bg-brand-600 rounded-xl hover:bg-brand-700 transition-all hover:-translate-y-0.5 shadow-lg shadow-brand-500/30 dark:shadow-brand-900/50">
                    <i class="fas fa-plus mr-2"></i> Buat Laporan Patroli
                </a>
                @endcan
            </div>

            <div class="relative z-10 hidden md:flex w-1/3 justify-end">
                <div class="w-40 h-40 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-xl shadow-slate-200/50 dark:shadow-slate-950/50 rounded-full flex items-center justify-center transform hover:scale-105 transition-transform duration-500 p-4">
                    <img src="{{ asset('assets/abc.png') }}" alt="Logo SERGAP 17" class="w-full h-full object-contain drop-shadow-md dark:drop-shadow-[0_0_10px_rgba(16,185,129,0.3)]">
                </div>
            </div>
        </div>

        {{-- 2. Stat Cards (Pastel + Dark Mode) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- Card 1 --}}
            <div class="bg-emerald-50 dark:bg-emerald-950/40 rounded-2xl p-6 border border-emerald-100 dark:border-emerald-900/50 flex flex-col items-center justify-center text-center hover:shadow-md dark:hover:shadow-slate-900/50 transition-all duration-300">
                <div class="w-14 h-14 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center text-emerald-500 dark:text-emerald-400 shadow-sm mb-4 transition-colors duration-300">
                    <i class="fas fa-tower-broadcast text-xl"></i>
                </div>
                <p class="text-sm text-emerald-600 dark:text-emerald-400 font-bold mb-1 transition-colors duration-300">Patroli Hari Ini</p>
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white transition-colors duration-300">{{ $activePatrolCount }}</p>
            </div>

            {{-- Card 2 --}}
            <div class="bg-amber-50 dark:bg-amber-950/40 rounded-2xl p-6 border border-amber-100 dark:border-amber-900/50 flex flex-col items-center justify-center text-center hover:shadow-md dark:hover:shadow-slate-900/50 transition-all duration-300">
                <div class="w-14 h-14 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center text-amber-500 dark:text-amber-400 shadow-sm mb-4 transition-colors duration-300">
                    <i class="fas fa-triangle-exclamation text-xl"></i>
                </div>
                <p class="text-sm text-amber-600 dark:text-amber-400 font-bold mb-1 transition-colors duration-300">Perlu Perhatian</p>
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white transition-colors duration-300">{{ $ongoingIncidentCount }}</p>
            </div>

            {{-- Card 3 --}}
            <div class="bg-blue-50 dark:bg-blue-950/40 rounded-2xl p-6 border border-blue-100 dark:border-blue-900/50 flex flex-col items-center justify-center text-center hover:shadow-md dark:hover:shadow-slate-900/50 transition-all duration-300">
                <div class="w-14 h-14 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center text-blue-500 dark:text-blue-400 shadow-sm mb-4 transition-colors duration-300">
                    <i class="fas fa-id-badge text-xl"></i>
                </div>
                <p class="text-sm text-blue-600 dark:text-blue-400 font-bold mb-1 transition-colors duration-300">Personil Bertugas</p>
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white transition-colors duration-300">{{ $onDutyCount }}</p>
            </div>

            {{-- Card 4 --}}
            <div class="bg-indigo-50 dark:bg-indigo-950/40 rounded-2xl p-6 border border-indigo-100 dark:border-indigo-900/50 flex flex-col items-center justify-center text-center hover:shadow-md dark:hover:shadow-slate-900/50 transition-all duration-300">
                <div class="w-14 h-14 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center text-indigo-500 dark:text-indigo-400 shadow-sm mb-4 transition-colors duration-300">
                    <i class="fas fa-map-location-dot text-xl"></i>
                </div>
                <p class="text-sm text-indigo-600 dark:text-indigo-400 font-bold mb-1 transition-colors duration-300">Cakupan Area</p>
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white transition-colors duration-300">{{ $averageRadiusMeters }} <span class="text-sm text-slate-500 dark:text-slate-400 font-medium">m</span></p>
            </div>

        </div>

        {{-- 3. Layout Bawah: Tabel & Sidebar Kanan --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Kolom Kiri: Tabel Aktivitas (Lebar 2/3) --}}
            <div id="aktivitas-patroli" class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col transition-colors duration-300">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                    <div>
                        <h5 class="text-lg font-bold text-slate-900 dark:text-white transition-colors duration-300">Aktivitas Patroli Terakhir</h5>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Pantau laporan patroli terbaru.</p>
                    </div>
                    @can('patrol-report view')
                    <a href="{{ route('patrol-reports.index') }}" class="text-sm font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 bg-brand-50 dark:bg-brand-950/50 hover:bg-brand-100 dark:hover:bg-brand-900/50 px-4 py-2 rounded-xl transition-colors shrink-0">
                        Lihat Semua
                    </a>
                    @endcan
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-sm text-left text-slate-600 dark:text-slate-400 whitespace-nowrap transition-colors duration-300">
                        <thead class="text-xs text-slate-400 dark:text-slate-500 uppercase bg-slate-50 dark:bg-slate-950/50 border-b border-slate-100 dark:border-slate-800 transition-colors duration-300">
                            <tr>
                                <th class="px-6 py-4 font-bold">No</th>
                                <th class="px-6 py-4 font-bold">Pos / Lokasi</th>
                                <th class="px-6 py-4 font-bold">Petugas</th>
                                <th class="px-6 py-4 font-bold">Waktu</th>
                                <th class="px-6 py-4 font-bold text-center">Status</th>
                                <th class="px-6 py-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentPatrolReports as $report)
                                <tr class="border-b border-slate-50 dark:border-slate-800/50 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                                    <td class="px-6 py-4 font-bold text-slate-500 dark:text-slate-400">
                                        {{ $recentPatrolReports->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-200">
                                        {{ $report->location->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-medium">
                                        {{ $report->user->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-medium text-xs">
                                        {{ \Carbon\Carbon::parse($report->patrol_at)->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $badge = 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-900/50';
                                            if ($report->status === 'mencurigakan') $badge = 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-400 dark:border-amber-900/50';
                                            if ($report->status === 'temuan_darurat') $badge = 'bg-red-100 text-red-700 border-red-200 dark:bg-red-950/50 dark:text-red-400 dark:border-red-900/50';
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border text-[10px] font-bold uppercase tracking-wider {{ $badge }}">
                                            <span class="w-1.5 h-1.5 rounded-full 
                                                @if($report->status === 'mencurigakan') bg-amber-500 
                                                @elseif($report->status === 'temuan_darurat') bg-red-500 
                                                @else bg-emerald-500 @endif">
                                            </span>
                                            {{ str_replace('_', ' ', $report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @can('patrol-report view')
                                        <a href="{{ route('patrol-reports.index', ['location_id' => $report->location_id]) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 text-slate-400 dark:text-slate-500 group-hover:text-brand-600 dark:group-hover:text-brand-400 group-hover:bg-brand-50 dark:group-hover:bg-brand-950/50 rounded-lg transition-all">
                                            <i class="fas fa-arrow-right text-xs"></i>
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800 mb-4 text-slate-300 dark:text-slate-600 text-2xl mx-auto transition-colors duration-300">
                                            <i class="fas fa-shield-halved"></i>
                                        </div>
                                        <p class="font-bold text-slate-600 dark:text-slate-300 transition-colors duration-300">Belum ada laporan patroli</p>
                                        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1 transition-colors duration-300">Laporan patroli dari pos Anda akan muncul di sini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($recentPatrolReports->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $recentPatrolReports->links() }}
                    </div>
                @endif
            </div>

            {{-- Kolom Kanan: Panel Aksi Cepat & Info (Lebar 1/3) --}}
            <div class="space-y-6">
                
                {{-- Panel Aksi Cepat --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm transition-colors duration-300">
                    <h5 class="text-lg font-bold text-slate-900 dark:text-white mb-4 transition-colors duration-300">Aksi Cepat</h5>
                    <div class="space-y-3">
                        @can('attendance view')
                        <a href="{{ route('attendances.index') }}" class="flex items-center gap-4 p-3 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-brand-200 dark:hover:border-brand-800 hover:bg-brand-50 dark:hover:bg-brand-950/30 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-brand-100 dark:bg-brand-950/50 text-brand-600 dark:text-brand-400 flex items-center justify-center shrink-0 group-hover:bg-brand-500 group-hover:text-white dark:group-hover:bg-brand-600 transition-colors">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Cek Absensi</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 transition-colors duration-300">Lihat kehadiran personil</p>
                            </div>
                        </a>
                        @endcan

                        @can('location view')
                        <a href="{{ route('locations.index') }}" class="flex items-center gap-4 p-3 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-blue-200 dark:hover:border-blue-800 hover:bg-blue-50 dark:hover:bg-blue-950/30 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 group-hover:bg-blue-500 group-hover:text-white dark:group-hover:bg-blue-600 transition-colors">
                                <i class="fas fa-map-location-dot"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Kontrol Lokasi</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 transition-colors duration-300">Atur radius pos jaga</p>
                            </div>
                        </a>
                        @endcan
                        
                        @can('leave-request view')
                        <a href="{{ route('leave-requests.index') }}" class="flex items-center gap-4 p-3 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-amber-200 dark:hover:border-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/30 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 group-hover:bg-amber-500 group-hover:text-white dark:group-hover:bg-amber-600 transition-colors">
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Pengajuan Izin</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 transition-colors duration-300">Tinjau izin personil</p>
                            </div>
                        </a>
                        @endcan
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection