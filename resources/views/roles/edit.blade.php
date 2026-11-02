@extends('layout.app')

@section('title', 'Edit Role')
@section('pageTitle', 'Edit Role')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    {{-- Header Section --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('roles.index') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Edit Role</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Perbarui nama role <span class="font-bold text-brand-600 dark:text-brand-400">{{ $role->name }}</span>.</p>
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
                <i class="fas fa-edit"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Informasi Role</h3>
        </div>

        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf
            @method('PUT')
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    
                    {{-- KOLOM KIRI: Input Form (Lebar 7/12) --}}
                    <div class="md:col-span-7 space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Nama Role <span class="text-rose-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" required
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all">
                        </div>
                    </div>

                    {{-- KOLOM KANAN: Panduan / Info (Lebar 5/12) --}}
                    <div class="md:col-span-5">
                        <div class="bg-blue-50/50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 rounded-2xl p-5 h-full flex flex-col justify-center transition-colors duration-300">
                            <h4 class="text-xs font-bold text-blue-800 dark:text-blue-200 uppercase tracking-wider mb-3 flex items-center gap-2 transition-colors duration-300">
                                <i class="fas fa-lightbulb text-blue-500 dark:text-blue-400"></i> Panduan Penulisan
                            </h4>
                            <ul class="text-sm text-blue-700/80 dark:text-blue-400/80 space-y-2 list-disc list-inside font-medium transition-colors duration-300">
                                <li>Gunakan huruf kecil (lowercase).</li>
                                <li>Gunakan tanda hubung (<span class="font-mono bg-white dark:bg-slate-800 px-1 rounded border border-blue-200 dark:border-blue-900">-</span>) sebagai pengganti spasi.</li>
                                <li>Contoh yang benar: <br> <span class="font-mono bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded border border-blue-200 dark:border-blue-900 text-blue-900 dark:text-blue-300 mt-1 inline-block">supervisor-lapangan</span> atau <span class="font-mono bg-white dark:bg-slate-800 px-1.5 py-0.5 rounded border border-blue-200 dark:border-blue-900 text-blue-900 dark:text-blue-300 inline-block">admin-gudang</span>.</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer Action --}}
            <div class="px-8 py-5 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3 transition-colors duration-300">
                <a href="{{ route('roles.index') }}" class="px-6 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection