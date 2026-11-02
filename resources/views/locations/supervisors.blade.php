@extends('layout.app')

@section('pageTitle', 'Hak Kelola Pos - ' . $location->name)

@push('styles')
<style>
    /* Toggle Switch Modern */
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 34px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    input:checked + .slider { background-color: #10b981; }
    input:checked + .slider:before { transform: translateX(20px); }

    /* Dark Mode: Switch mati jadi lebih gelap */
    .dark .slider { background-color: #475569; }
    .dark input:checked + .slider { background-color: #10b981; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">

    {{-- Header Section --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('locations.index') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Hak Kelola Pos</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Atur izin akses untuk pos <span class="font-bold text-brand-600 dark:text-brand-400">{{ $location->name }}</span>.</p>
        </div>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 rounded-2xl shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-3 text-emerald-800 dark:text-emerald-400 transition-colors duration-300">
            <i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 text-xl"></i>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-emerald-500 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-2xl shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-2 text-rose-800 dark:text-rose-400 font-bold text-sm mb-2 transition-colors duration-300">
            <i class="fas fa-exclamation-circle text-lg"></i> Terjadi kesalahan
        </div>
        <ul class="list-disc list-inside text-sm text-rose-600 dark:text-rose-400 space-y-1 font-medium ml-1 transition-colors duration-300">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Info Banner --}}
    <div class="flex items-start gap-4 p-5 bg-blue-50/70 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 rounded-3xl text-blue-800 dark:text-blue-300 shadow-sm transition-colors duration-300">
        <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-900 text-blue-500 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm transition-colors duration-300">
            <i class="fas fa-info-circle text-lg"></i>
        </div>
        <div class="text-sm">
            <p class="font-bold text-base mb-1 text-blue-900 dark:text-blue-200 transition-colors duration-300">Informasi Hak Akses</p>
            <p class="text-blue-700/80 dark:text-blue-400/80 transition-colors duration-300">Hak yang diatur di sini <strong>hanya berlaku untuk pos "{{ $location->name }}"</strong>. Jika Anda ingin user yang sama mengelola pos lain, atur kembali melalui halaman Hak Kelola Pos di pos tersebut. User dengan <strong>akses penuh</strong> (super-admin / pemegang izin "Kelola Semua Lokasi") otomatis bisa mengelola semua pos dan tidak perlu diatur di sini.</p>
        </div>
    </div>

    {{-- RINGKASAN: SIAPA YANG PUNYA AKSES KE POS INI --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm p-6 space-y-5 transition-colors duration-300">
        <div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Siapa yang punya akses ke pos ini</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Ringkasan akses efektif untuk pos <span class="font-bold">{{ $location->name }}</span>.</p>
        </div>

        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2 transition-colors duration-300">
                <i class="fas fa-crown text-emerald-500 mr-1"></i> Akses penuh (semua pos)
            </p>
            <div class="flex flex-wrap gap-2">
                @forelse($fullAccessRoles as $roleName)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-400 text-xs font-bold transition-colors duration-300">
                        <i class="fas fa-crown text-[10px]"></i> {{ $roleName }}
                    </span>
                @empty
                    <span class="text-sm text-slate-400 dark:text-slate-500">-</span>
                @endforelse
            </div>
        </div>

        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2 transition-colors duration-300">
                <i class="fas fa-user-shield text-indigo-500 mr-1"></i> Akses khusus pos "{{ $location->name }}"
            </p>
            <div class="flex flex-wrap gap-2">
                @forelse($rebacUsers as $u)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/50 text-indigo-700 dark:text-indigo-400 text-xs font-bold transition-colors duration-300">
                        {{ $u->name }}
                        <span class="font-medium opacity-80">({{ implode(', ', $rebacByUser[(string) $u->id] ?? []) }})</span>
                    </span>
                @empty
                    <span class="text-sm text-slate-400 dark:text-slate-500">Belum ada user yang diberi hak khusus untuk pos ini.</span>
                @endforelse
            </div>
        </div>
    </div>

    {{-- LEGENDA AKSI LENGKAP --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach($actions as $action)
        <div class="p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition-colors duration-300
                    @if($action == 'view') bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400
                    @elseif($action == 'update') bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400
                    @elseif($action == 'delete') bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400
                    @elseif($action == 'assign') bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400
                    @elseif($action == 'export') bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400
                    @else bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 @endif">
                    @if($action == 'view') <i class="fas fa-eye text-xs"></i>
                    @elseif($action == 'update') <i class="fas fa-pen text-xs"></i>
                    @elseif($action == 'delete') <i class="fas fa-trash-alt text-xs"></i>
                    @elseif($action == 'assign') <i class="fas fa-user-shield text-xs"></i>
                    @elseif($action == 'export') <i class="fas fa-file-export text-xs"></i>
                    @else <i class="fas fa-cog text-xs"></i> @endif
                </div>
                <dt class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide transition-colors duration-300">{{ $action }}</dt>
            </div>
            <dd class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed transition-colors duration-300">
                @if($action == 'view') Boleh melihat data pos ini (mis. riwayat absensi & laporan patroli).
                @elseif($action == 'update') Boleh mengubah data pos ini (nama, alamat, radius, dll).
                @elseif($action == 'delete') Boleh menghapus pos ini dari sistem.
                @elseif($action == 'assign') Boleh menugaskan/mengatur jadwal shift personel di pos ini.
                @elseif($action == 'export') Boleh mengekspor laporan patroli pos ini ke Excel/PDF.
                @else Akses khusus sistem. @endif
            </dd>
        </div>
        @endforeach
    </div>

    {{-- Main Form Section --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden flex flex-col mt-6 transition-colors duration-300">

        {{-- Filter Role --}}
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 flex flex-col sm:flex-row items-center justify-between gap-4 transition-colors duration-300">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300">Pengaturan Hak Akses</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Pilih role, lalu geser saklar untuk memberikan izin.</p>
            </div>
            <form method="GET" action="{{ route('locations.supervisors.edit', $location->id) }}" class="w-full sm:w-auto">
                <div class="flex items-center gap-2">
                    <label for="role" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden sm:block whitespace-nowrap transition-colors duration-300">Filter Role:</label>
                    <select name="role" id="role" onchange="this.form.submit()"
                            class="w-full sm:w-48 px-4 py-2.5 text-sm font-bold border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-sm appearance-none cursor-pointer transition-colors duration-300">
                        @forelse($roles as $role)
                            <option value="{{ $role }}" @selected($selectedRole === $role)>{{ $role }}</option>
                        @empty
                            <option value="">Belum ada role</option>
                        @endforelse
                    </select>
                </div>
            </form>
        </div>

        @if($roles->isEmpty() || $candidates->isEmpty())
            {{-- Empty State --}}
            <div class="px-6 py-24 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-50 dark:bg-slate-800/50 mb-4 border border-slate-100 dark:border-slate-800 transition-colors duration-300">
                    <i class="fas fa-user-slash text-4xl text-slate-300 dark:text-slate-600"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 mb-1 transition-colors duration-300">Tidak Ada Data</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 transition-colors duration-300">
                    @if($roles->isEmpty()) Belum ada role terdaftar di sistem. @else Belum ada user dengan role "{{ $selectedRole }}". @endif
                </p>
            </div>
        @else
            <form action="{{ route('locations.supervisors.update', $location->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="role" value="{{ $selectedRole }}">

                {{-- List Pengguna & Switch --}}
                <div class="p-4 sm:p-6 space-y-3">
                    @foreach($candidates as $candidate)
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-brand-300 dark:hover:border-brand-700 hover:shadow-md transition-all bg-white dark:bg-slate-900 group">

                        {{-- Info User --}}
                        <div class="flex items-center gap-4 lg:w-1/3 min-w-0">
                            <div class="w-12 h-12 rounded-full bg-slate-900 dark:bg-slate-800 text-white flex items-center justify-center font-black text-lg shrink-0 shadow-sm overflow-hidden transition-colors duration-300">
                                @if($candidate->profile)
                                    <img src="{{ asset('storage/' . $candidate->profile) }}"
                                         alt="Avatar {{ $candidate->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-extrabold text-slate-800 dark:text-slate-200 text-base truncate transition-colors duration-300">{{ $candidate->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate transition-colors duration-300">{{ $candidate->email }}</p>
                            </div>
                        </div>

                        @if($fullAccess[$candidate->id] ?? false)
                            {{-- Akses penuh: saklar tidak relevan --}}
                            <div class="lg:w-2/3 flex lg:justify-end">
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-400 text-sm font-bold transition-colors duration-300">
                                    <i class="fas fa-shield-alt"></i> Akses penuh ke semua pos
                                </span>
                            </div>
                        @else
                            {{-- Switch Actions --}}
                            <div class="flex flex-wrap items-center gap-4 sm:gap-6 lg:w-2/3 justify-start lg:justify-end">
                                @foreach($actions as $action)
                                <div class="flex flex-col items-center gap-1.5">
                                    {{-- Label Teks di atas Switch --}}
                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300">{{ $action }}</span>
                                    {{-- Switch --}}
                                    <label class="switch" title="Beri akses {{ $action }}">
                                        <input type="checkbox"
                                               name="permissions[{{ $candidate->id }}][]"
                                               value="{{ $action }}"
                                               @checked($matrix[$candidate->id][$action] ?? false)>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Action Bar --}}
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 transition-colors duration-300">
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-shield-alt text-brand-500 dark:text-brand-400 text-base"></i> Hak akses akan langsung diterapkan setelah disimpan.
                    </p>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a href="{{ route('locations.index') }}" class="w-full sm:w-auto text-center px-6 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200">
                            <i class="fas fa-save"></i> Simpan Hak Akses
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection