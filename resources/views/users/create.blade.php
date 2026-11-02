@extends('layout.app')

@section('title', 'Tambah User')
@section('pageTitle', 'Tambah User')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    {{-- Header Section --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('users.index') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Tambah Personel</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Buat akun pengguna baru beserta role akses awalnya.</p>
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

    {{-- Form Card (Diperlebar & di-grid) --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden transition-colors duration-300">
        <div class="px-8 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 flex items-center gap-3 transition-colors duration-300">
            <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-950/50 text-brand-600 dark:text-brand-400 flex items-center justify-center transition-colors duration-300">
                <i class="fas fa-user-plus"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Informasi Akun</h3>
        </div>

        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="p-8 space-y-6">
                
                {{-- Nama Lengkap (Full Width) --}}
                <div>
                    <label for="name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap personel"
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Username --}}
                    <div>
                        <label for="username" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Username <span class="text-rose-500">*</span></label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required placeholder="Username unik"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Email <span class="text-rose-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="alamat@email.com"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Password <span class="text-rose-500">*</span></label>
                        <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1.5 font-medium flex items-center gap-1 transition-colors duration-300"><i class="fas fa-info-circle"></i> Wajib minimal 8 karakter.</p>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Konfirmasi Password <span class="text-rose-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password"
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                    </div>
                </div>

                {{-- Role (Full Width) --}}
                <div>
                    <label for="role" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Role Akses <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <select id="role" name="role" required
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all appearance-none cursor-pointer">
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role Pengguna --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors duration-300">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer (Action Buttons) --}}
            <div class="px-8 py-5 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3 transition-colors duration-300">
                <a href="{{ route('users.index') }}" class="px-6 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200">
                    <i class="fas fa-save"></i> Simpan Personel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection