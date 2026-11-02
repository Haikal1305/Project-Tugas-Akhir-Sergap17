@extends('layout.app')

@section('title', 'Reset Password User')
@section('pageTitle', 'Reset Password User')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    {{-- Header Section --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('users.index') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Reset Password</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Buat kata sandi baru untuk akun personel ini.</p>
        </div>
    </div>

    {{-- Error Validation Alert --}}
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
                <i class="fas fa-key"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Keamanan Akun</h3>
        </div>

        <form method="POST" action="{{ route('users.password.update', $user) }}">
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
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                                        <i class="fas fa-at text-slate-400 dark:text-slate-500"></i> {{ $user->username }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Info Peringatan --}}
                        <div class="flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-900/50 rounded-2xl text-amber-800 dark:text-amber-400 transition-colors duration-300">
                            <i class="fas fa-exclamation-triangle mt-0.5 text-amber-500 dark:text-amber-400"></i>
                            <p class="text-xs font-medium leading-relaxed transition-colors duration-300">Setelah password diubah, harap informasikan kata sandi baru kepada personel yang bersangkutan agar mereka dapat login kembali.</p>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: Form Password (Lebar 7/12) --}}
                    <div class="md:col-span-7 space-y-5">
                        {{-- Kata Sandi Baru --}}
                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Kata Sandi Baru <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-500 dark:text-brand-400 transition-colors duration-300">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                            </div>
                        </div>

                        {{-- Konfirmasi Kata Sandi Baru --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-500 dark:text-brand-400 transition-colors duration-300">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi baru"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                            </div>
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
                    <i class="fas fa-save"></i> Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection