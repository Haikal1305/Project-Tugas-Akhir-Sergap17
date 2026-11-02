@extends('layout.app')

@section('pageTitle', 'Riwayat Absensi')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Riwayat Absensi</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">
                @can('attendance view all')
                    Pantau seluruh riwayat kehadiran personel di semua pos.
                @else
                    Riwayat kehadiran dan jam shift Anda di pos jaga.
                @endcan
            </p>
        </div>

        @can('attendance create')
        <a href="{{ route('attendances.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200 hover:-translate-y-0.5">
            <i class="fas fa-fingerprint"></i> Absen Sekarang
        </a>
        @endcan
    </div>

    {{-- Info Banner --}}
    <div class="flex items-start gap-4 p-5 bg-blue-50/70 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 rounded-3xl text-blue-800 dark:text-blue-300 shadow-sm transition-colors duration-300">
        <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-900 text-blue-500 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm transition-colors duration-300">
            <i class="fas fa-info-circle text-lg"></i>
        </div>
        <div class="text-sm">
            @can('attendance view all')
                <p class="font-bold text-base mb-1 text-blue-900 dark:text-blue-200 transition-colors duration-300">Akses Global Admin</p>
                <p class="text-blue-700/80 dark:text-blue-400/80 transition-colors duration-300">Anda melihat data dari seluruh pos. Riwayat kehadiran semua personel tampil di sini.</p>
            @elseif($hasManagedLocations)
                <p class="font-bold text-base mb-1 text-blue-900 dark:text-blue-200 transition-colors duration-300">Akses Multi-Pos</p>
                <p class="text-blue-700/80 dark:text-blue-400/80 transition-colors duration-300">Anda melihat data absensi Anda sendiri dan pos yang Anda kelola (melalui menu Kontrol Lokasi &rarr; Hak Kelola Pos).</p>
            @else
                <p class="font-bold text-base mb-1 text-blue-900 dark:text-blue-200 transition-colors duration-300">Akses Personal</p>
                <p class="text-blue-700/80 dark:text-blue-400/80 transition-colors duration-300">Anda hanya melihat riwayat absensi milik Anda sendiri. Jika Admin memberi hak kelola pos, data personel di pos tersebut akan muncul di sini.</p>
            @endcan
        </div>
    </div>

    {{-- Alert Success / Error --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 rounded-2xl shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-3 text-emerald-800 dark:text-emerald-400 transition-colors duration-300">
            <i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 text-xl"></i>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-emerald-500 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors"><i class="fas fa-times"></i></button>
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-2xl shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-3 text-rose-800 dark:text-rose-400 transition-colors duration-300">
            <i class="fas fa-exclamation-circle text-rose-500 dark:text-rose-400 text-xl"></i>
            <p class="text-sm font-bold">{{ session('error') }}</p>
        </div>
        <button @click="show = false" class="text-rose-500 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 transition-colors"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- DAFTAR CARD ABSENSI --}}
    <div class="space-y-4">
        @forelse($attendances as $index => $attendance)
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm hover:shadow-md dark:hover:shadow-slate-950/50 transition-all duration-300 overflow-hidden group">
            <div class="flex flex-col lg:flex-row lg:items-stretch">
                
                {{-- Kiri: Tanggal & Petugas --}}
                <div class="p-5 lg:p-6 lg:w-72 bg-slate-50/50 dark:bg-slate-950/50 border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-slate-800 shrink-0 flex flex-row lg:flex-col justify-between lg:justify-center items-center lg:items-start gap-4 transition-colors duration-300">
                    <div class="flex items-center gap-3">
                        @can('attendance view all')
                            <div class="w-10 h-10 rounded-full bg-slate-900 dark:bg-slate-800 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0 overflow-hidden transition-colors duration-300">
                                @if($attendance->user && $attendance->user->profile)
                                    <img src="{{ asset('storage/' . $attendance->user->profile) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($attendance->user->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 dark:text-slate-200 text-sm leading-tight transition-colors duration-300">{{ $attendance->user->name ?? '-' }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium transition-colors duration-300">Petugas</p>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-xl bg-brand-100 dark:bg-brand-950/50 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-sm shrink-0 transition-colors duration-300">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 dark:text-slate-200 text-sm leading-tight transition-colors duration-300">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l') }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium transition-colors duration-300">Hari Kerja</p>
                            </div>
                        @endcan
                    </div>
                    <div class="text-right lg:text-left">
                        <div class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700 shadow-sm transition-colors duration-300">
                            <i class="far fa-calendar-alt text-brand-500 dark:text-brand-400"></i> {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d M Y') }}
                        </div>
                    </div>
                </div>

                {{-- Tengah: Detail Shift & Pos --}}
                <div class="p-5 lg:p-6 flex-1 grid grid-cols-1 sm:grid-cols-3 gap-6 items-center min-w-0 transition-colors duration-300">
                    {{-- Pos Jaga --}}
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 transition-colors duration-300">Pos Jaga</p>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                            <i class="fas fa-map-marker-alt text-brand-500 dark:text-brand-400"></i> {{ $attendance->location->name ?? '-' }}
                        </span>
                    </div>

                    {{-- Shift --}}
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 transition-colors duration-300">Jadwal Shift</p>
                        @php
                            $pivot = $attendance->location?->users()->where('users.id', $attendance->user_id)->first()?->pivot;
                        @endphp
                        @if($pivot && ($pivot->shift_start || $pivot->shift_end))
                            <span class="inline-flex items-center text-xs font-bold text-slate-700 dark:text-slate-300 transition-colors duration-300">
                                {{ $pivot->shift_start ? \Carbon\Carbon::parse($pivot->shift_start)->format('H:i') : '--:--' }} 
                                <i class="fas fa-arrow-right mx-2 text-slate-300 dark:text-slate-600 text-[10px] transition-colors duration-300"></i>
                                {{ $pivot->shift_end ? \Carbon\Carbon::parse($pivot->shift_end)->format('H:i') : '--:--' }}
                            </span>
                        @else
                            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 italic transition-colors duration-300">Fleksibel</span>
                        @endif
                    </div>

                    {{-- Realisasi Jam --}}
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 transition-colors duration-300">Realisasi Jam</p>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1.5" title="Jam Masuk">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="font-bold text-slate-700 dark:text-slate-300 text-sm transition-colors duration-300">{{ $attendance->clock_in_at ? \Carbon\Carbon::parse($attendance->clock_in_at)->format('H:i') : '--:--' }}</span>
                            </div>
                            <span class="text-slate-300 dark:text-slate-600 transition-colors duration-300">|</span>
                            <div class="flex items-center gap-1.5" title="Jam Keluar">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                <span class="font-bold text-slate-700 dark:text-slate-300 text-sm transition-colors duration-300">{{ $attendance->clock_out_at ? \Carbon\Carbon::parse($attendance->clock_out_at)->format('H:i') : '--:--' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Status, Foto, Aksi --}}
                <div class="p-5 lg:p-6 lg:w-64 border-t lg:border-t-0 lg:border-l border-slate-100 dark:border-slate-800 flex items-center justify-between lg:justify-end gap-4 shrink-0 bg-slate-50/30 dark:bg-slate-950/30 transition-colors duration-300">
                    {{-- Status --}}
                    <div class="text-left lg:text-right">
                        @php
                            $statusStyle = match($attendance->status) {
                                'hadir' => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900/50',
                                'terlambat' => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-900/50',
                                'pulang_cepat' => 'bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-900/50',
                                'izin' => 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-900/50',
                                'alfa' => 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-900/50',
                                default => 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700',
                            };
                            $statusLabel = match($attendance->status) {
                                'hadir' => 'Hadir',
                                'terlambat' => 'Terlambat',
                                'pulang_cepat' => 'Pulang Cepat',
                                'izin' => 'Izin',
                                'alfa' => 'Alfa',
                                default => 'Belum Hadir',
                            };
                        @endphp
                        <div class="flex items-center justify-end gap-1.5 mb-2">
                            <span class="inline-flex items-center px-3 py-1 text-[11px] font-bold rounded-lg border {{ $statusStyle }} transition-colors duration-300">
                                {{ $statusLabel }}
                            </span>
                            @if($attendance->notes)
                            <i class="fas fa-info-circle text-slate-300 dark:text-slate-600 hover:text-slate-500 dark:hover:text-slate-400 cursor-help transition-colors" title="{{ $attendance->notes }}"></i>
                            @endif
                        </div>
                        
                        {{-- Foto Bukti --}}
                        <div class="flex items-center justify-end gap-2">
                            @if($attendance->clock_in_photo)
                                <a href="{{ asset('storage/' . $attendance->clock_in_photo) }}" target="_blank" class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900/50 hover:bg-indigo-600 dark:hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Foto Masuk">
                                    <i class="fas fa-camera text-xs"></i>
                                </a>
                            @endif
                            @if($attendance->clock_out_photo)
                                <a href="{{ asset('storage/' . $attendance->clock_out_photo) }}" target="_blank" class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-100 dark:border-purple-900/50 hover:bg-purple-600 dark:hover:bg-purple-600 hover:text-white transition-all shadow-sm" title="Foto Keluar">
                                    <i class="fas fa-camera-retro text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Hapus --}}
                    @can('attendance delete')
                    <div class="pl-4 border-l border-slate-200 dark:border-slate-700 transition-colors duration-300">
                        <form action="{{ route('attendances.destroy', $attendance->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data absensi ini?');" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg text-slate-300 dark:text-slate-600 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 flex items-center justify-center transition-colors" title="Hapus Absensi">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        {{-- Empty State --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm py-24 text-center flex flex-col items-center transition-colors duration-300">
            <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mb-4 border border-slate-100 dark:border-slate-800 transition-colors duration-300">
                <i class="fas fa-clipboard-list text-4xl text-slate-300 dark:text-slate-600"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 mb-1 transition-colors duration-300">Belum Ada Riwayat</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto transition-colors duration-300">Riwayat kehadiran Anda akan muncul di sini secara otomatis setelah melakukan Clock In.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($attendances->hasPages())
    <div class="mt-8">
        {{ $attendances->links() }}
    </div>
    @endif
</div>
@endsection