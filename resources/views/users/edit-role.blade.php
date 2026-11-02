@extends('layout.app')

@section('title', 'Ubah Role User')
@section('pageTitle', 'Ubah Role User')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    {{-- Header Section --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('users.index') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Ubah Role Akses</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Perbarui hak akses sistem untuk personel ini.</p>
        </div>
    </div>

    @if ($errors->any())
    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-2xl shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-exclamation-circle text-rose-600 dark:text-rose-400 text-lg"></i>
            <p class="text-sm font-bold text-rose-800 dark:text-rose-400">Periksa kembali input Anda:</p>
        </div>
        <ul class="list-disc list-inside text-sm text-rose-600 dark:text-rose-400 space-y-1 font-medium ml-1 transition-colors duration-300">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Card (Wide Layout) --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden transition-colors duration-300">
        
        {{-- Card Header --}}
        <div class="px-8 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 flex items-center gap-3 transition-colors duration-300">
            <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-950/50 text-brand-600 dark:text-brand-400 flex items-center justify-center transition-colors duration-300">
                <i class="fas fa-user-shield"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Pengaturan Role</h3>
        </div>

        <form method="POST" action="{{ route('users.role.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    
                    {{-- KOLOM KIRI: Info User & Peringatan (Lebar 5/12) --}}
                    <div class="md:col-span-5 space-y-5">
                        {{-- User Profile Info --}}
                        <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-2xl p-5 flex items-center gap-4 transition-colors duration-300">
                            <div class="w-14 h-14 rounded-full bg-slate-900 dark:bg-slate-950 text-white flex items-center justify-center font-bold text-xl shrink-0 shadow-sm border-2 border-white dark:border-slate-800 transition-colors duration-300">
                                @if($user->profile)
                                    <img src="{{ asset('storage/' . $user->profile) }}" alt="Avatar" class="w-full h-full object-cover rounded-full">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-base font-bold text-slate-800 dark:text-slate-200 truncate transition-colors duration-300">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate transition-colors duration-300">{{ $user->email }}</p>
                                <div class="mt-1.5 flex items-center gap-1.5">
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider transition-colors duration-300">Role Saat Ini:</span>
                                    @if($user->roles->first())
                                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400 rounded border border-brand-200 dark:border-brand-900/50 transition-colors duration-300">
                                            {{ $user->roles->first()->name }}
                                        </span>
                                    @else
                                        <span class="text-[10px] italic text-slate-400 dark:text-slate-500 transition-colors duration-300">Belum ada</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Info Peringatan --}}
                        <div class="flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-900/50 rounded-2xl text-amber-800 dark:text-amber-400 transition-colors duration-300">
                            <i class="fas fa-exclamation-triangle mt-0.5 text-amber-500 dark:text-amber-400"></i>
                            <p class="text-xs font-medium leading-relaxed transition-colors duration-300">Mengubah role akan mengganti hak akses pengguna secara langsung. Pastikan role yang dipilih sudah sesuai dengan tugas personel.</p>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: Form Ubah Role (Lebar 7/12) --}}
                    <div class="md:col-span-7 space-y-5">
                        <div>
                            <label for="role" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Pilih Role Baru <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-500 dark:text-brand-400 transition-colors duration-300">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <select id="role" name="role" required
                                    class="w-full pl-11 pr-10 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all appearance-none cursor-pointer">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}" {{ old('role', $user->roles->first()->name ?? '') === $role ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors duration-300">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 font-medium flex items-center gap-1.5 transition-colors duration-300"><i class="fas fa-info-circle text-brand-500 dark:text-brand-400"></i> Role sebelumnya akan digantikan sepenuhnya oleh role yang dipilih.</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Action Footer --}}
            <div class="px-8 py-5 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3 transition-colors duration-300">
                <a href="{{ route('users.index') }}" class="px-6 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200">
                    <i class="fas fa-save"></i> Simpan Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection