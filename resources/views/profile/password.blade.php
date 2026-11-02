@extends('layout.app')

@section('title', 'Ubah Password')
@section('pageTitle', 'Ubah Kata Sandi')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    {{-- Header Section --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('profile.edit') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Ubah Kata Sandi</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Pastikan akun Anda menggunakan sandi yang aman.</p>
        </div>
    </div>

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
    <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 rounded-2xl shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-3 text-emerald-800 dark:text-emerald-400 transition-colors duration-300">
            <i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 text-xl"></i>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-emerald-500 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors"><i class="fas fa-times"></i></button>
    </div>
    @endif

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
            <div class="w-10 h-10 rounded-full bg-brand-100 dark:bg-brand-950/50 text-brand-600 dark:text-brand-400 flex items-center justify-center shrink-0 transition-colors duration-300">
                <i class="fas fa-shield-halved text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Keamanan Akun</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 transition-colors duration-300">Ganti kata sandi secara berkala untuk keamanan.</p>
            </div>
        </div>

        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    
                    {{-- KOLOM KIRI: Panduan (Lebar 5/12) --}}
                    <div class="md:col-span-5">
                        <div class="bg-blue-50/50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 rounded-2xl p-5 h-full flex flex-col justify-center transition-colors duration-300">
                            <h4 class="text-xs font-bold text-blue-800 dark:text-blue-200 uppercase tracking-wider mb-3 flex items-center gap-2 transition-colors duration-300">
                                <i class="fas fa-lightbulb text-blue-500 dark:text-blue-400"></i> Tips Sandi Aman
                            </h4>
                            <ul class="text-sm text-blue-700/80 dark:text-blue-400/80 space-y-2 list-disc list-inside font-medium transition-colors duration-300">
                                <li>Gunakan minimal 8 karakter.</li>
                                <li>Kombinasikan huruf besar, huruf kecil, dan angka.</li>
                                <li>Hindari menggunakan nama sendiri atau tanggal lahir.</li>
                                <li>Jangan gunakan sandi yang sama dengan akun lain.</li>
                            </ul>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: Form Password (Lebar 7/12) --}}
                    <div class="md:col-span-7 space-y-5">
                        {{-- Kata Sandi Saat Ini --}}
                        <div>
                            <label for="current_password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors duration-300">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password" id="current_password" name="current_password" required placeholder="Masukkan sandi saat ini"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                            </div>
                        </div>

                        <div class="border-t border-slate-100 dark:border-slate-800 my-2 transition-colors duration-300"></div>

                        {{-- Kata Sandi Baru --}}
                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Kata Sandi Baru <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-500 dark:text-brand-400 transition-colors duration-300">
                                    <i class="fas fa-key"></i>
                                </div>
                                <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                            </div>
                        </div>

                        {{-- Konfirmasi Kata Sandi Baru --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Konfirmasi Sandi Baru <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-500 dark:text-brand-400 transition-colors duration-300">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi sandi baru"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Action Footer --}}
            <div class="px-8 py-5 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3 transition-colors duration-300">
                <a href="{{ route('profile.edit') }}" class="px-6 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200">
                    <i class="fas fa-save"></i> Perbarui Sandi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection