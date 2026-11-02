@extends('layout.app')

@section('title', 'Kelola Hak Akses Role')
@section('pageTitle', 'Kelola Hak Akses')

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    {{-- Header Section --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('roles.index') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Kelola Hak Akses</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Atur izin fitur untuk role <span class="font-bold text-brand-600 dark:text-brand-400">{{ $role->name }}</span>.</p>
        </div>
    </div>

    {{-- Info Banner --}}
    <div class="flex items-start gap-4 p-5 bg-blue-50/70 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 rounded-2xl text-blue-800 dark:text-blue-300 shadow-sm transition-colors duration-300">
        <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-900 text-blue-500 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm mt-0.5 transition-colors duration-300">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="text-sm">
            <p class="font-bold mb-1 text-blue-900 dark:text-blue-200 transition-colors duration-300">Informasi Cakupan Hak Akses</p>
            <p class="text-blue-700/80 dark:text-blue-400/80 transition-colors duration-300">Hak akses di halaman ini berlaku <strong>secara global</strong> (ke seluruh pos). Ini berbeda dengan menu <em>Hak Kelola Pos</em> di Kontrol Lokasi yang pengaturannya spesifik per-pos.</p>
        </div>
    </div>

    @if ($errors->any())
    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-2xl shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-exclamation-circle text-rose-600 dark:text-rose-400 text-lg"></i>
            <p class="text-sm font-bold text-rose-800 dark:text-rose-400">Terjadi kesalahan:</p>
        </div>
        <ul class="list-disc list-inside text-sm text-rose-600 dark:text-rose-400 space-y-1 font-medium ml-1 transition-colors duration-300">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Section --}}
    <form method="POST" action="{{ route('roles.permissions.update', $role) }}">
        @csrf
        @method('PUT')

        {{-- Grid Modul --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($permissions as $moduleLabel => $modulePermissions)
                
                {{-- Card per Modul --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col overflow-hidden transition-colors duration-300">
                    
                    {{-- Module Header --}}
                    <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 flex items-center gap-2 transition-colors duration-300">
                        <div class="w-6 h-6 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 shrink-0 transition-colors duration-300">
                            <i class="fas fa-folder text-[10px]"></i>
                        </div>
                        <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider truncate transition-colors duration-300">
                            {{ $moduleLabel }}
                        </h3>
                    </div>

                    {{-- Module Body (List Toggles) --}}
                    <div class="flex flex-col">
                        @foreach ($modulePermissions as $permission)
                            @php
                                $inputId = 'perm_' . $permission->id;
                                $isChecked = in_array($permission->name, old('permissions', $rolePermissions));
                            @endphp
                            
                            {{-- PERUBAHAN: div diganti label, onclick dihapus --}}
                            <label class="flex items-center justify-between py-2.5 px-4 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors border-b border-slate-50 dark:border-slate-800/50 last:border-0 group cursor-pointer">
                                
                                <div class="flex flex-col pr-3">
                                    {{-- PERUBAHAN: tag label di dalam diganti span --}}
                                    <span class="text-[13px] font-semibold text-slate-700 dark:text-slate-300 cursor-pointer select-none group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors duration-300">
                                        {{ $permission->label }}
                                    </span>
                                    
                                    {{-- Badge Peringatan --}}
                                    @if($permission->isBroadAccess)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 rounded border border-amber-200 dark:border-amber-900/50 transition-colors duration-300" title="Berlaku untuk semua pos">
                                            <i class="fas fa-exclamation-triangle"></i> Semua Pos
                                        </span>
                                    </div>
                                    @endif
                                </div>

                                {{-- Custom Toggle Switch (Lebih kecil & rapi) --}}
                                {{-- PERUBAHAN: tag label di sini dihilangkan karena parent-nya sudah label --}}
                                <div class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" id="{{ $inputId }}" name="permissions[]" value="{{ $permission->name }}" {{ $isChecked ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-500 transition-colors duration-300"></div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="col-span-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm py-20 text-center flex flex-col items-center transition-colors duration-300">
                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mb-4 border border-slate-100 dark:border-slate-800 transition-colors duration-300">
                        <i class="fas fa-shield-alt text-3xl text-slate-300 dark:text-slate-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 mb-1 transition-colors duration-300">Belum Ada Hak Akses</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 transition-colors duration-300">Data hak akses belum ditambahkan ke dalam sistem.</p>
                </div>
            @endforelse
        </div>

        {{-- Action Bar (Rapi di bawah form) --}}
        <div class="mt-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 transition-colors duration-300">
            <div class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400 transition-colors duration-300">
                <i class="fas fa-shield-alt text-brand-500 dark:text-brand-400 text-lg"></i>
                <span>Pastikan pengaturan hak akses sudah sesuai.</span>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('roles.index') }}" class="w-full sm:w-auto text-center px-6 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200">
                    <i class="fas fa-save"></i> Simpan Hak Akses
                </button>
            </div>
        </div>
    </form>
</div>
@endsection