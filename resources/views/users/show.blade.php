@extends('layout.app')

@section('title', 'Detail User')
@section('pageTitle', 'Detail User')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    {{-- Header Section --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('users.index') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Profil Personel</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Informasi detail akun pengguna.</p>
        </div>
    </div>

    {{-- Main Profile Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden flex flex-col transition-colors duration-300">
        
        <div class="flex flex-col md:flex-row">
            {{-- KOLOM KIRI: Avatar & Identitas --}}
            <div class="md:w-5/12 p-8 flex flex-col items-center justify-center text-center border-b md:border-b-0 md:border-r border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-950/30 relative transition-colors duration-300">
                {{-- Ornamen Latar Halus --}}
                <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05]" style="background-image: radial-gradient(circle at 2px 2px, #888 1px, transparent 0); background-size: 24px 24px;"></div>
                
                <div class="relative z-10 w-full">
                    {{-- Avatar Besar --}}
                    <div class="w-32 h-32 rounded-full bg-white dark:bg-slate-800 p-1.5 shadow-md mx-auto mb-5 border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                        <div class="w-full h-full rounded-full bg-slate-900 dark:bg-slate-950 text-white flex items-center justify-center font-black text-5xl overflow-hidden shadow-inner transition-colors duration-300">
                            @if($user->profile)
                                <img src="{{ asset('storage/' . $user->profile) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                    </div>

                    {{-- Nama & Email --}}
                    <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white truncate transition-colors duration-300">{{ $user->name }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1 flex items-center justify-center gap-1.5 truncate transition-colors duration-300">
                        <i class="fas fa-envelope text-slate-400 dark:text-slate-500"></i> {{ $user->email }}
                    </p>

                    {{-- Role Badge --}}
                    <div class="mt-5 flex flex-wrap justify-center gap-2">
                        @forelse ($user->roles as $role)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-bold bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400 border border-brand-200 dark:border-brand-900/50 shadow-sm transition-colors duration-300">
                                <i class="fas fa-shield-alt mr-1.5"></i> {{ $role->name }}
                            </span>
                        @empty
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                                Belum ada role
                            </span>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: Detail Informasi (List Style) --}}
            <div class="md:w-7/12 p-8 flex flex-col justify-between transition-colors duration-300">
                <div>
                    <h4 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-5 flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-info-circle"></i> Informasi Akun
                    </h4>
                    
                    <div class="space-y-4">
                        {{-- Username --}}
                        <div class="flex items-center justify-between gap-4 py-3 border-b border-slate-100 dark:border-slate-800 last:border-0 transition-colors duration-300">
                            <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400 transition-colors duration-300">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                                    <i class="fas fa-at text-slate-400 dark:text-slate-500"></i>
                                </div>
                                <span class="text-sm font-semibold">Username</span>
                            </div>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">{{ $user->username }}</span>
                        </div>

                        {{-- Terdaftar Sejak --}}
                        <div class="flex items-center justify-between gap-4 py-3 border-b border-slate-100 dark:border-slate-800 last:border-0 transition-colors duration-300">
                            <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400 transition-colors duration-300">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                                    <i class="fas fa-calendar-plus text-slate-400 dark:text-slate-500"></i>
                                </div>
                                <span class="text-sm font-semibold">Terdaftar Sejak</span>
                            </div>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">{{ $user->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>

                        {{-- Terakhir Diperbarui --}}
                        <div class="flex items-center justify-between gap-4 py-3 border-b border-slate-100 dark:border-slate-800 last:border-0 transition-colors duration-300">
                            <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400 transition-colors duration-300">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                                    <i class="fas fa-clock-rotate-left text-slate-400 dark:text-slate-500"></i>
                                </div>
                                <span class="text-sm font-semibold">Terakhir Diperbarui</span>
                            </div>
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">{{ $user->updated_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-8 mt-4 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-colors shadow-sm">
                        Kembali
                    </a>

                    @can('user update role')
                    <a href="{{ route('users.role.edit', $user) }}" class="w-full sm:w-auto text-center inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 text-sm font-bold rounded-xl transition-colors">
                        <i class="fas fa-user-shield"></i> Ubah Role
                    </a>
                    @endcan

                    @can('user update')
                    <a href="{{ route('users.edit', $user) }}" class="w-full sm:w-auto text-center inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200">
                        <i class="fas fa-pen"></i> Edit Data
                    </a>
                    @endcan
                </div>
            </div>
        </div>

    </div>
</div>
@endsection