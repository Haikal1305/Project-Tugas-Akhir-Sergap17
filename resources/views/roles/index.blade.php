@extends('layout.app')

@section('title', 'Manajemen Role')
@section('pageTitle', 'Manajemen Role')


@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Manajemen Role</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Kelola role beserta hak akses (permission) di dalam sistem.</p>
        </div>

        @can('role create')
        <a href="{{ route('roles.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200 hover:-translate-y-0.5">
            <i class="fas fa-plus"></i> Tambah Role
        </a>
        @endcan
    </div>

    {{-- Flash Message --}}
    @if (session('success'))
    <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 rounded-2xl shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-3 text-emerald-800 dark:text-emerald-400 transition-colors duration-300">
            <i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 text-xl"></i>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-emerald-500 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if (session('error'))
    <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-2xl shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-3 text-rose-800 dark:text-rose-400 transition-colors duration-300">
            <i class="fas fa-exclamation-circle text-rose-500 dark:text-rose-400 text-xl"></i>
            <p class="text-sm font-bold">{{ session('error') }}</p>
        </div>
        <button @click="show = false" class="text-rose-500 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 transition-colors"><i class="fas fa-times"></i></button>
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
            <form method="GET" action="{{ route('roles.index') }}" class="flex gap-2 w-full max-w-md">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 transition-colors duration-300"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama role..." 
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
                        <th class="px-6 py-5">Nama Role</th>
                        <th class="px-6 py-5 text-center">Jumlah Permission</th>
                        <th class="px-6 py-5 text-center">Jumlah User</th>
                        <th class="px-6 py-5 text-right min-w-[250px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm transition-colors duration-300">
                    @forelse ($roles as $role)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800 dark:text-slate-200 text-base transition-colors duration-300">{{ $role->name }}</span>
                                @if ($role->name === 'super-admin')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50 shadow-sm transition-colors duration-300">
                                        <i class="fas fa-star mr-1 text-[9px]"></i> Bawaan Sistem
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                                {{ $role->name === 'super-admin' ? 'Semua Akses' : $role->permissions_count . ' Izin' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                                {{ $role->users_count }} User
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center justify-end gap-1.5">
                                @if ($role->name !== 'super-admin')
                                    @can('role update permission')
                                    <a href="{{ route('roles.permissions.edit', $role) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-600 dark:hover:bg-indigo-600 hover:text-white rounded-lg transition-all border border-indigo-200 dark:border-indigo-900/50 shadow-sm" title="Kelola Permission">
                                        <i class="fas fa-key text-[10px]"></i> Akses
                                    </a>
                                    @endcan

                                    @can('role update')
                                    <a href="{{ route('roles.edit', $role) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 hover:bg-amber-500 dark:hover:bg-amber-500 hover:text-white rounded-lg transition-all border border-amber-200 dark:border-amber-900/50 shadow-sm" title="Edit Role">
                                        <i class="fas fa-pen text-[10px]"></i> Edit
                                    </a>
                                    @endcan

                                    @can('role delete')
                                    <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Yakin ingin menghapus role {{ $role->name }}?');" class="inline-block m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/50 hover:bg-rose-600 dark:hover:bg-rose-600 hover:text-white rounded-lg transition-all border border-rose-200 dark:border-rose-900/50 shadow-sm" title="Hapus Role">
                                            <i class="fas fa-trash-alt text-[10px]"></i> Hapus
                                        </button>
                                    </form>
                                    @endcan
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500 italic pr-2 bg-slate-50 dark:bg-slate-800/50 px-3 py-1.5 rounded-lg border border-slate-100 dark:border-slate-800 transition-colors duration-300">Tidak dapat diubah</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-24 text-center">
                            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-50 dark:bg-slate-800/50 mb-4 border border-slate-100 dark:border-slate-800 transition-colors duration-300">
                                <i class="fas fa-user-shield text-4xl text-slate-300 dark:text-slate-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 mb-1 transition-colors duration-300">Tidak Ada Data</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 transition-colors duration-300">Data role tidak ditemukan atau belum ditambahkan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($roles->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 transition-colors duration-300">
            {{ $roles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection