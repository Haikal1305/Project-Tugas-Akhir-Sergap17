@extends('layout.app')

@section('title', 'Data Personil')
@section('pageTitle', 'Data Personil')


@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Data Personil</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Kelola akun pengguna dan role akses sistem.</p>
        </div>

        @can('user create')
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200 hover:-translate-y-0.5">
            <i class="fas fa-plus"></i> Tambah User
        </a>
        @endcan
    </div>

    {{-- Alert --}}
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
            <p class="text-sm font-bold text-rose-800 dark:text-rose-400">Terjadi kesalahan:</p>
        </div>
        <ul class="list-disc list-inside text-sm text-rose-600 dark:text-rose-400 space-y-1 font-medium ml-1 transition-colors duration-300">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Main Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden flex flex-col transition-colors duration-300">
        
        {{-- Search Toolbar --}}
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 flex flex-col sm:flex-row items-center justify-between gap-4 transition-colors duration-300">
            <form method="GET" action="{{ route('users.index') }}" class="flex gap-2 w-full max-w-md">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 transition-colors duration-300"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, atau email..." 
                           class="w-full pl-11 pr-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 bg-white dark:bg-slate-800 text-slate-800 dark:text-white transition-all shadow-sm placeholder:text-slate-400 dark:placeholder:text-slate-500">
                </div>
                <button type="submit" class="px-6 py-3 text-sm font-bold bg-slate-900 dark:bg-brand-600 hover:bg-slate-800 dark:hover:bg-brand-500 text-white rounded-xl transition-colors shadow-sm">
                    Cari
                </button>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300">
                        <th class="px-6 py-5">Nama Personel</th>
                        <th class="px-6 py-5">Username</th>
                        <th class="px-6 py-5">Email</th>
                        <th class="px-6 py-5">Role Akses</th>
                        <th class="px-6 py-5 text-right min-w-[280px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm transition-colors duration-300">
                    @forelse ($users as $user)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-900 dark:bg-slate-800 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-sm overflow-hidden transition-colors duration-300">
                                    @if($user->profile)
                                        <img src="{{ asset('storage/' . $user->profile) }}" alt="Avatar" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <span class="font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium transition-colors duration-300">{{ $user->username }}</td>
                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-medium transition-colors duration-300">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @forelse ($user->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400 border border-brand-100 dark:border-brand-900/50 shadow-sm transition-colors duration-300">
                                    <i class="fas fa-shield-alt mr-1.5 text-[10px]"></i> {{ $role->name }}
                                </span>
                            @empty
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                                    Belum ada role
                                </span>
                            @endforelse
                        </td>
                        <td class="px-6 py-4">
                            {{-- AKSI MENGGUNAKAN TEKS (TEXT PILLS) --}}
                            <div class="flex flex-wrap items-center justify-end gap-1.5">
                                
                                {{-- Detail --}}
                                <a href="{{ route('users.show', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                                    <i class="fas fa-eye text-[10px]"></i> Detail
                                </a>

                                @can('user update')
                                    {{-- Edit --}}
                                    <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-brand-700 dark:text-brand-400 bg-brand-50 dark:bg-brand-950/50 hover:bg-brand-100 dark:hover:bg-brand-900/50 rounded-lg transition-colors border border-brand-200 dark:border-brand-900/50">
                                        <i class="fas fa-pen text-[10px]"></i> Edit
                                    </a>
                                    
                                    {{-- Reset Password --}}
                                    <a href="{{ route('users.password.edit', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 hover:bg-amber-100 dark:hover:bg-amber-900/50 rounded-lg transition-colors border border-amber-200 dark:border-amber-900/50">
                                        <i class="fas fa-key text-[10px]"></i> Password
                                    </a>
                                @endcan

                                @can('user update role')
                                    @if (auth()->id() !== $user->id)
                                        {{-- Ubah Role --}}
                                        <a href="{{ route('users.role.edit', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-lg transition-colors border border-indigo-200 dark:border-indigo-900/50">
                                            <i class="fas fa-user-shield text-[10px]"></i> Role
                                        </a>
                                    @endif
                                @endcan

                                @can('user delete')
                                    @if (auth()->id() !== $user->id)
                                        {{-- Hapus --}}
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Yakin ingin menghapus user {{ $user->name }}?');" class="inline-block m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/50 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-lg transition-colors border border-rose-200 dark:border-rose-900/50">
                                                <i class="fas fa-trash-alt text-[10px]"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-24 text-center">
                            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-50 dark:bg-slate-800/50 mb-4 border border-slate-100 dark:border-slate-800 transition-colors duration-300">
                                <i class="fas fa-users-slash text-4xl text-slate-300 dark:text-slate-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 mb-1 transition-colors duration-300">Tidak Ada Data</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 transition-colors duration-300">Data pengguna tidak ditemukan atau belum ditambahkan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 transition-colors duration-300">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection