@extends('layout.app')

@section('pageTitle', 'Data Pos Jaga')

@push('styles')
<style>
    /* Scrollbar halus untuk daftar personel di dalam kartu */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Dark Mode Scrollbar */
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }
</style>
@endpush

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Pos Jaga</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Kelola data lokasi penugasan dan pantau personel yang bertugas.</p>
        </div>
        
        @can('location create')
        <a href="{{ route('locations.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200 hover:-translate-y-0.5">
            <i class="fas fa-plus"></i> Tambah Pos Baru
        </a>
        @endcan
    </div>

    {{-- Info Banner --}}
    @if(!auth()->user()->can('location manage-all'))
    <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 rounded-2xl text-blue-800 dark:text-blue-300 shadow-sm transition-colors duration-300">
        <i class="fas fa-circle-info mt-0.5 text-blue-500 dark:text-blue-400"></i>
        <div class="text-sm">
            <p class="font-bold text-blue-900 dark:text-blue-200 transition-colors duration-300">Informasi Hak Akses</p>
            <p class="text-blue-600 dark:text-blue-400 mt-0.5 transition-colors duration-300">Anda hanya melihat pos yang sudah diberi hak lihat oleh Admin, dan tombol aksi di setiap kartu hanya muncul sesuai hak yang diberikan untuk pos tersebut.</p>
        </div>
    </div>
    @endif

    {{-- Alert Success --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 rounded-2xl shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-3 text-emerald-800 dark:text-emerald-400 transition-colors duration-300">
            <i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 text-xl"></i>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-emerald-500 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    {{-- GRID CARDS SECTION --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-6">
        @forelse($locations as $location)
        @php
            // Hak ReBAC user ini pada pos ini (saklar di halaman Hak Kelola Pos)
            $abilities = $abilitiesByLocation[$location->id] ?? [];
            $canAssign = auth()->user()->can('location assign') && in_array('assign', $abilities, true);
            $canUpdate = auth()->user()->can('location update') && in_array('update', $abilities, true);
            $canDelete = auth()->user()->can('location delete') && in_array('delete', $abilities, true);
        @endphp
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-xl dark:hover:shadow-slate-950/50 hover:border-brand-200 dark:hover:border-brand-800 transition-all duration-300 group flex flex-col relative overflow-hidden">
            
            {{-- Ornamen Latar Belakang --}}
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-slate-50 dark:bg-slate-800 rounded-full opacity-50 group-hover:bg-brand-50 dark:group-hover:bg-brand-950/50 group-hover:scale-110 transition-all duration-500 pointer-events-none"></div>

            {{-- Header Kartu (Nama & Radius) --}}
            <div class="relative z-10 flex justify-between items-start mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-900 dark:bg-slate-800 text-white flex items-center justify-center font-black text-lg shadow-md shrink-0 group-hover:bg-brand-600 dark:group-hover:bg-brand-600 transition-colors duration-300">
                        <i class="fas fa-map-location-dot"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-lg leading-tight truncate transition-colors duration-300">{{ $location->name }}</h3>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">
                            <i class="fas fa-location-dot text-slate-400 dark:text-slate-500 shrink-0"></i>
                            <span class="truncate max-w-[140px]" title="{{ $location->address }}">{{ $location->address ?: 'Alamat belum diatur' }}</span>
                        </div>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 shrink-0 transition-colors duration-300">
                    {{ $location->radius }} m
                </span>
            </div>

            {{-- Body Kartu (Daftar Personel) --}}
            <div class="relative z-10 flex-1 mt-2 mb-6 border-t border-slate-100 dark:border-slate-800 pt-5 transition-colors duration-300">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3 transition-colors duration-300">Personel Bertugas</p>
                @if($location->users->isNotEmpty())
                    <div class="space-y-2 max-h-32 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($location->users as $user)
                            <div class="flex items-center justify-between bg-slate-50/80 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 px-3 py-2 rounded-xl group-hover:bg-white dark:group-hover:bg-slate-800 transition-colors">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-brand-100 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400 flex items-center justify-center font-bold text-[10px] shrink-0 overflow-hidden transition-colors duration-300">
                                        @if($user->profile)
                                            <img src="{{ asset('storage/' . $user->profile) }}" alt="Avatar" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 text-xs truncate max-w-[100px] transition-colors duration-300">{{ $user->name }}</span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700 shadow-sm shrink-0 transition-colors duration-300">
                                    @if($user->pivot->shift_start && $user->pivot->shift_end)
                                        {{ \Carbon\Carbon::parse($user->pivot->shift_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($user->pivot->shift_end)->format('H:i') }}
                                    @else
                                        Fleksibel
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-5 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-dashed border-slate-200 dark:border-slate-700 transition-colors duration-300">
                        <i class="fas fa-user-slash text-slate-300 dark:text-slate-600 mb-1 text-lg"></i>
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-semibold transition-colors duration-300">Belum ada personel</span>
                    </div>
                @endif
            </div>

            {{-- Footer Kartu (Tombol Aksi Teks) --}}
            {{-- Tombol muncul hanya jika punya izin Spatie DAN hak ReBAC pada pos ini --}}
            <div class="relative z-10 flex flex-wrap items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800 mt-auto transition-colors duration-300">
                @if($canAssign)
                <a href="{{ route('locations.assignment.edit', $location->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-600 dark:hover:bg-indigo-600 hover:text-white rounded-lg transition-all border border-indigo-200 dark:border-indigo-900/50 shadow-sm" title="Penugasan Personel">
                    <i class="fas fa-user-shield text-[10px]"></i> Tugaskan
                </a>
                @endif

                @if(auth()->user()->can('location manage-all'))
                <a href="{{ route('locations.supervisors.edit', $location->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/50 hover:bg-purple-600 dark:hover:bg-purple-600 hover:text-white rounded-lg transition-all border border-purple-200 dark:border-purple-900/50 shadow-sm" title="Hak Kelola Pos">
                    <i class="fas fa-user-lock text-[10px]"></i> Akses
                </a>
                @elseif(in_array('view', $abilities, true))
                {{-- Pemegang hak VIEW: halaman baca-saja --}}
                <a href="{{ route('locations.access.show', $location->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/50 hover:bg-purple-600 dark:hover:bg-purple-600 hover:text-white rounded-lg transition-all border border-purple-200 dark:border-purple-900/50 shadow-sm" title="Lihat Akses Pos">
                    <i class="fas fa-eye text-[10px]"></i> Akses
                </a>
                @endif

                @if($canUpdate)
                <a href="{{ route('locations.edit', $location->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 hover:bg-amber-500 dark:hover:bg-amber-500 hover:text-white rounded-lg transition-all border border-amber-200 dark:border-amber-900/50 shadow-sm" title="Edit Data">
                    <i class="fas fa-pen text-[10px]"></i> Edit
                </a>
                @endif

                @if($canDelete)
                <form action="{{ route('locations.destroy', $location->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('Yakin ingin menghapus pos ini? Data yang dihapus tidak dapat dikembalikan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/50 hover:bg-rose-600 dark:hover:bg-rose-600 hover:text-white rounded-lg transition-all border border-rose-200 dark:border-rose-900/50 shadow-sm" title="Hapus Data">
                        <i class="fas fa-trash-alt text-[10px]"></i> Hapus
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        {{-- Empty State (Jika tidak ada data) --}}
        <div class="col-span-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm py-24 text-center flex flex-col items-center justify-center transition-colors duration-300">
            <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mb-5 border border-slate-100 dark:border-slate-800 transition-colors duration-300">
                <i class="fas fa-map-location-dot text-4xl text-slate-300 dark:text-slate-600"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 mb-2 transition-colors duration-300">
                @if(auth()->user()->can('location manage-all')) Belum ada Pos Jaga @else Belum ada pos yang bisa Anda lihat @endif
            </h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-6 transition-colors duration-300">
                @if(auth()->user()->can('location manage-all'))
                    Silakan tambahkan pos jaga baru untuk mulai mengelola lokasi dan menugaskan personel.
                @else
                    Hubungi Admin untuk diberi hak lihat pada pos yang menjadi tanggung jawab Anda.
                @endif
            </p>
            @can('location create')
            <a href="{{ route('locations.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-slate-800 dark:hover:bg-brand-500 transition-colors">
                <i class="fas fa-plus"></i> Tambah Pos Sekarang
            </a>
            @endcan
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($locations->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $locations->links() }}
    </div>
    @endif
</div>
@endsection