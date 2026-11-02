@extends('layout.app')

@section('pageTitle', 'Akses Pos - ' . $location->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    {{-- Header Section --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('locations.index') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Akses Pos</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Siapa saja yang punya akses ke pos <span class="font-bold text-brand-600 dark:text-brand-400">{{ $location->name }}</span>.</p>
        </div>
    </div>

    {{-- Info Banner (Baca-saja) --}}
    <div class="flex items-start gap-4 p-5 bg-blue-50/70 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 rounded-3xl text-blue-800 dark:text-blue-300 shadow-sm transition-colors duration-300">
        <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-900 text-blue-500 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm transition-colors duration-300">
            <i class="fas fa-eye text-lg"></i>
        </div>
        <div class="text-sm">
            <p class="font-bold text-base mb-1 text-blue-900 dark:text-blue-200 transition-colors duration-300">Hanya Tampilan</p>
            <p class="text-blue-700/80 dark:text-blue-400/80 transition-colors duration-300">Halaman ini hanya menampilkan hak akses yang berlaku. Perubahan hak akses dilakukan oleh Admin pusat.</p>
        </div>
    </div>

    {{-- Akses penuh (semua pos) --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm p-6 space-y-3 transition-colors duration-300">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 transition-colors duration-300">
            <i class="fas fa-crown text-emerald-500 mr-1"></i> Akses penuh (semua pos)
        </p>
        <div class="flex flex-wrap gap-2">
            @forelse($fullAccessRoles as $roleName)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-400 text-xs font-bold transition-colors duration-300">
                    <i class="fas fa-crown text-[10px]"></i> {{ $roleName }}
                </span>
            @empty
                <span class="text-sm text-slate-400 dark:text-slate-500">-</span>
            @endforelse
        </div>
    </div>

    {{-- Akses khusus pos ini --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm p-6 space-y-4 transition-colors duration-300">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 transition-colors duration-300">
            <i class="fas fa-user-shield text-indigo-500 mr-1"></i> Akses khusus pos "{{ $location->name }}"
        </p>

        @forelse($rebacUsers as $u)
            @php $granted = $rebacByUser[(string) $u->id] ?? []; @endphp
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-md transition-all bg-white dark:bg-slate-900">
                <div class="flex items-center gap-4 lg:w-1/3 min-w-0">
                    <div class="w-12 h-12 rounded-full bg-slate-900 dark:bg-slate-800 text-white flex items-center justify-center font-black text-lg shrink-0 shadow-sm overflow-hidden transition-colors duration-300">
                        @if($u->profile)
                            <img src="{{ asset('storage/' . $u->profile) }}" alt="Avatar {{ $u->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        @endif
                    </div>
                    <p class="font-extrabold text-slate-800 dark:text-slate-200 text-base truncate transition-colors duration-300">
                        {{ $u->name }}
                        @if($u->id === auth()->id())
                            <span class="ml-1 text-[10px] font-bold text-brand-600 dark:text-brand-400 uppercase">Anda</span>
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 lg:w-2/3 justify-start lg:justify-end">
                    @foreach($actions as $action)
                        @if(in_array($action, $granted, true))
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wider transition-colors duration-300">
                                <i class="fas fa-check mr-1 text-[9px]"></i> {{ $action }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 text-[10px] font-bold uppercase tracking-wider line-through transition-colors duration-300">
                                {{ $action }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400 dark:text-slate-500">Belum ada user yang diberi hak khusus untuk pos ini.</p>
        @endforelse
    </div>
</div>
@endsection