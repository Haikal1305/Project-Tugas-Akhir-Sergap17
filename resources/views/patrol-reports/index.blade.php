@extends('layout.app')

@section('pageTitle', 'Monitoring Patroli')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white transition-colors duration-300">Monitoring Patroli</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Pantau laporan pengamanan dan kondisi pos jaga secara real-time.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @can('location manage-all')
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors">
                    <i class="fas fa-building"></i>
                </div>
                <select id="accessLocationPicker" class="pl-11 pr-8 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 shadow-sm transition-all appearance-none cursor-pointer">
                    <option value="">-- Kelola Akses Pos --</option>
                    @foreach($locations as $loc)
                        <option value="{{ route('locations.supervisors.edit', $loc->id) }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
            @endcan

            @can('patrol-report create')
            <a href="{{ route('patrol-reports.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white font-bold text-sm rounded-xl hover:bg-brand-700 transition-all shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 hover:-translate-y-0.5">
                <i class="fas fa-plus"></i> Kirim Laporan
            </a>
            @endcan
        </div>
    </div>

    {{-- Info Banner --}}
    <div class="flex items-start gap-3 p-4 bg-white/60 dark:bg-slate-900/60 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm text-slate-600 dark:text-slate-400 transition-colors duration-300">
        <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 mt-0.5 transition-colors duration-300">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="text-sm">
            @can('patrol-report manage-all')
                <p class="font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Akses Global Admin</p>
                <p class="mt-0.5">Anda melihat laporan seluruh pos. Laporan patroli semua personel di semua pos tampil di sini.</p>
            @elseif($hasManagedLocations)
                <p class="font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Akses Multi-Pos</p>
                <p class="mt-0.5">Anda melihat laporan Anda sendiri dan pos yang Anda kelola (melalui menu Kontrol Lokasi &rarr; Hak Kelola Pos).</p>
            @else
                <p class="font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Akses Personal</p>
                <p class="mt-0.5">Anda hanya melihat laporan patroli milik Anda sendiri. Jika Admin memberi hak kelola pos, laporan tersebut akan muncul di sini.</p>
            @endcan
        </div>
    </div>

    {{-- Alert Flash Message --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-400 rounded-2xl text-sm font-bold flex items-center gap-3 shadow-sm transition-colors duration-300">
            <i class="fas fa-check-circle text-xl text-emerald-500 dark:text-emerald-400"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-400 rounded-2xl text-sm font-bold flex items-center gap-3 shadow-sm transition-colors duration-300">
            <i class="fas fa-exclamation-circle text-xl text-rose-500 dark:text-rose-400"></i> {{ session('error') }}
        </div>
    @endif

    {{-- TABLE MODERN (DATA GRID STYLE) --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden flex flex-col transition-colors duration-300">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                {{-- Sticky Header --}}
                <thead class="sticky top-0 z-10 bg-slate-50/90 dark:bg-slate-950/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
                    <tr class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300">
                        <th class="px-6 py-5 whitespace-nowrap">Waktu</th>
                        <th class="px-6 py-5 whitespace-nowrap">Petugas</th>
                        <th class="px-6 py-5 whitespace-nowrap">Pos Jaga</th>
                        <th class="px-6 py-5 whitespace-nowrap">Status</th>
                        <th class="px-6 py-5 min-w-[250px]">Catatan Laporan</th>
                        <th class="px-6 py-5 text-center whitespace-nowrap">Bukti</th>
                        <th class="px-6 py-5 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm transition-colors duration-300">
                    @forelse($reports as $report)
                    {{-- Row Group (Hover state) --}}
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group">
                        
                        {{-- Waktu --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-900 dark:text-white text-base transition-colors duration-300">{{ $report->patrol_at->format('H:i') }}</span>
                                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mt-0.5 transition-colors duration-300">{{ $report->date->format('d M Y') }}</span>
                            </div>
                        </td>

                        {{-- Petugas --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm ring-2 ring-white dark:ring-slate-900 overflow-hidden transition-colors duration-300">
                                    @if($report->user && $report->user->profile)
                                        <img src="{{ asset('storage/' . $report->user->profile) }}" alt="Avatar" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($report->user->name ?? 'U', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">{{ $report->user->name ?? '-' }}</span>
                                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase transition-colors duration-300">Personel</span>
                                </div>
                            </div>
                        </td>

                        {{-- Pos Jaga --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold border border-slate-200 dark:border-slate-700 shadow-sm transition-colors duration-300">
                                <i class="fas fa-map-marker-alt text-brand-500 dark:text-brand-400"></i>
                                {{ $report->location->name ?? '-' }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($report->status === 'aman')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50 shadow-sm transition-colors duration-300">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Aman
                                </span>
                            @elseif($report->status === 'mencurigakan')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50 shadow-sm transition-colors duration-300">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Mencurigakan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 shadow-sm transition-colors duration-300">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span> Darurat
                                </span>
                            @endif
                        </td>

                        {{-- Catatan Laporan --}}
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 leading-snug transition-colors duration-300" title="{{ $report->report }}">
                                "{{ $report->report }}"
                            </p>
                        </td>

                        {{-- Bukti Foto --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($report->photo)
                                <a href="{{ asset('storage/' . $report->photo) }}" target="_blank" class="relative inline-block w-11 h-11 rounded-xl overflow-hidden border-2 border-white dark:border-slate-800 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700 hover:ring-brand-500 dark:hover:ring-brand-400 hover:scale-110 transition-all duration-200 group/img">
                                    <img src="{{ asset('storage/' . $report->photo) }}" alt="Foto" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover/img:opacity-100 flex items-center justify-center transition-opacity">
                                        <i class="fas fa-search-plus text-white text-xs"></i>
                                    </div>
                                </a>
                            @else
                                <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-slate-300 dark:text-slate-600 border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                                    <i class="fas fa-image"></i>
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            @can('patrol-report delete')
                            <form action="{{ route('patrol-reports.destroy', $report->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus laporan patroli ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-9 h-9 inline-flex items-center justify-center text-slate-300 dark:text-slate-600 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-xl transition-all opacity-50 group-hover:opacity-100" title="Hapus Laporan">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-24 text-center">
                            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-50 dark:bg-slate-800/50 mb-4 border border-slate-100 dark:border-slate-800 transition-colors duration-300">
                                <i class="fas fa-folder-open text-4xl text-slate-300 dark:text-slate-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Belum Ada Laporan</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto transition-colors duration-300">Data laporan patroli yang tercatat akan muncul di sini secara otomatis.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($reports->hasPages())
        <div class="p-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 mt-auto transition-colors duration-300">
            {{ $reports->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('accessLocationPicker')?.addEventListener('change', function () {
        if (this.value) window.location.href = this.value;
    });
</script>
@endpush