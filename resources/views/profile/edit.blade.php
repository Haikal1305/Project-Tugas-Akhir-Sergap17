@extends('layout.app')

@section('title', 'Pengaturan Akun')
@section('pageTitle', 'Pengaturan Akun')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    {{-- Header Section --}}
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Pengaturan Akun</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Perbarui nama profil dan foto avatar akun Anda.</p>
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

    {{-- Main Form Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden transition-colors duration-300" 
         x-data="{ preview: '{{ $user->profile ? asset('storage/' . $user->profile) : '' }}' }">
        
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="flex flex-col md:flex-row">
                
                {{-- SISI KIRI: Foto Profil & Identitas --}}
                <div class="p-8 md:w-5/12 bg-slate-50/50 dark:bg-slate-950/50 border-b md:border-b-0 md:border-r border-slate-100 dark:border-slate-800 flex flex-col items-center justify-center text-center transition-colors duration-300">
                    
                    {{-- Avatar Preview --}}
                    <div class="relative group">
                        <div class="w-32 h-32 rounded-full bg-white dark:bg-slate-800 border-4 border-slate-100 dark:border-slate-700 shadow-lg flex items-center justify-center overflow-hidden shrink-0 mx-auto relative z-10 transition-colors duration-300">
                            <template x-if="preview">
                                <img :src="preview" alt="Avatar Preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!preview">
                                <span class="text-4xl font-black text-slate-300 dark:text-slate-600 transition-colors duration-300">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </template>
                        </div>
                        
                        {{-- Tombol Ganti Foto (Overlay) --}}
                        <label for="profile" class="absolute bottom-0 right-0 z-20 w-10 h-10 bg-brand-600 hover:bg-brand-700 text-white rounded-full flex items-center justify-center cursor-pointer shadow-md transition-transform hover:scale-110 border-2 border-white dark:border-slate-900">
                            <i class="fas fa-camera text-sm"></i>
                            <input type="file" name="profile" id="profile" accept="image/*" class="hidden"
                                   @change="const file = $event.target.files[0]; if (file) { preview = URL.createObjectURL(file); }">
                        </label>
                    </div>

                    <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-200 mt-4 transition-colors duration-300">{{ $user->name }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium transition-colors duration-300">{{ $user->email }}</p>

                    <div class="mt-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 transition-colors duration-300">
                            <i class="fas fa-image"></i> Maks. 2MB (JPG/PNG)
                        </span>
                    </div>
                </div>

                {{-- SISI KANAN: Form Data Diri --}}
                <div class="p-8 md:w-7/12 flex flex-col justify-center">
                    
                    <div class="space-y-5">
                        {{-- Input Nama --}}
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors duration-300">
                                    <i class="fas fa-user"></i>
                                </div>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all">
                            </div>
                        </div>

                        {{-- Input Username (Readonly) --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="username" class="block text-sm font-bold text-slate-700 dark:text-slate-300 transition-colors duration-300">Username</label>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider transition-colors duration-300"><i class="fas fa-lock mr-1"></i> Terkunci</span>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors duration-300">
                                    <i class="fas fa-at"></i>
                                </div>
                                <input type="text" id="username" value="{{ $user->username }}" disabled
                                    class="w-full pl-11 pr-4 py-3 bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-500 dark:text-slate-400 cursor-not-allowed transition-colors duration-300">
                            </div>
                        </div>

                        {{-- Input Email (Readonly) --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 transition-colors duration-300">Alamat Email</label>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider transition-colors duration-300"><i class="fas fa-lock mr-1"></i> Terkunci</span>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors duration-300">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input type="email" id="email" value="{{ $user->email }}" disabled
                                    class="w-full pl-11 pr-4 py-3 bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-500 dark:text-slate-400 cursor-not-allowed transition-colors duration-300">
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5 font-medium flex items-center gap-1 transition-colors duration-300"><i class="fas fa-info-circle"></i> Hubungi Admin jika ingin mengubah Username atau Email.</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Action Footer --}}
            <div class="px-8 py-5 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 transition-colors duration-300">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection