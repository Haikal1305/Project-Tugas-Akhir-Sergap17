@extends('layout.app')

@section('pageTitle', 'Penugasan Personel - ' . $location->name)

@push('styles')
<style>
    /* Toggle Switch Modern ala iOS */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 28px;
        flex-shrink: 0;
    }
    .switch input { 
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 34px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    input:checked + .slider {
        background-color: #10b981;
    }
    input:checked + .slider:before {
        transform: translateX(22px);
    }

    /* Dark Mode: Switch mati jadi lebih gelap */
    .dark .slider {
        background-color: #475569;
    }
    .dark input:checked + .slider {
        background-color: #10b981;
    }

    /* Transisi form input */
    .shift-form-enter {
        transition: all 0.3s ease-out;
    }
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
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Penugasan Personel</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Atur personel dan jam shift untuk pos <span class="font-bold text-brand-600 dark:text-brand-400">{{ $location->name }}</span>.</p>
        </div>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 rounded-2xl text-emerald-800 dark:text-emerald-400 shadow-sm transition-colors duration-300">
        <i class="fas fa-check-circle text-xl text-emerald-500 dark:text-emerald-400"></i>
        <p class="text-sm font-bold">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-2xl text-rose-800 dark:text-rose-400 shadow-sm transition-colors duration-300">
        <i class="fas fa-exclamation-circle text-xl text-rose-500 dark:text-rose-400"></i>
        <p class="text-sm font-bold">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Form Section --}}
    <form action="{{ route('locations.assignment.update', $location->id) }}" method="POST" class="relative">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
            @forelse($users as $user)
            @php
                $isAssigned = $assignedUsers->has($user->id);
                $assignedData = $assignedUsers->get($user->id);
                $shiftStart = $isAssigned && $assignedData->pivot->shift_start ? \Carbon\Carbon::parse($assignedData->pivot->shift_start)->format('H:i') : '';
                $shiftEnd = $isAssigned && $assignedData->pivot->shift_end ? \Carbon\Carbon::parse($assignedData->pivot->shift_end)->format('H:i') : '';
                
                $oldChecked = old('user_ids') !== null ? in_array($user->id, old('user_ids')) : $isAssigned;
                $oldShiftStart = old("shifts.{$user->id}.start", $shiftStart);
                $oldShiftEnd = old("shifts.{$user->id}.end", $shiftEnd);
            @endphp
            
            {{-- Kartu Personel --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm transition-all duration-300 relative group flex flex-col h-full"
                 x-data="{ active: {{ $oldChecked ? 'true' : 'false' }} }"
                 :class="active ? 'ring-2 ring-brand-500/20 dark:ring-brand-500/30 border-brand-500 dark:border-brand-600' : 'hover:border-brand-300 dark:hover:border-brand-700'">
                
                {{-- Header Kartu --}}
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4 min-w-0">
                        @if($user->profile)
                            <img src="{{ asset('storage/' . $user->profile) }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-slate-100 dark:border-slate-700 shadow-sm shrink-0">
                        @else
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg shrink-0 transition-colors shadow-sm"
                                 :class="active ? 'bg-brand-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 dark:text-slate-200 text-base leading-tight truncate transition-colors duration-300">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 truncate transition-colors duration-300">{{ $user->email }}</p>
                        </div>
                    </div>

                    {{-- Toggle Switch --}}
                    <label class="switch mt-1">
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" x-model="active" {{ $oldChecked ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>

                {{-- Area Jam Shift (Animasi Muncul) --}}
                <div x-show="active" x-collapse x-cloak class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2 transition-colors duration-300">
                        <i class="far fa-clock"></i> Pengaturan Jam Shift
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1 transition-colors duration-300">Jam Masuk</label>
                            <input type="time" name="shifts[{{ $user->id }}][start]" value="{{ $oldShiftStart }}"
                                   class="w-full px-3 py-2 text-sm font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all">
                        </div>
                        <span class="text-slate-300 dark:text-slate-600 font-bold mt-5 transition-colors duration-300">—</span>
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1 transition-colors duration-300">Jam Pulang</label>
                            <input type="time" name="shifts[{{ $user->id }}][end]" value="{{ $oldShiftEnd }}"
                                   class="w-full px-3 py-2 text-sm font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all">
                        </div>
                    </div>
                </div>

                {{-- Placeholder saat Switch Mati (Menjaga tinggi kartu) --}}
                <div x-show="!active" x-cloak class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-800 flex-1 flex items-center justify-center transition-colors duration-300">
                    <p class="text-xs font-medium text-slate-400 dark:text-slate-500 italic transition-colors duration-300">Geser saklar untuk mengaktifkan shift</p>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm py-20 text-center flex flex-col items-center transition-colors duration-300">
                <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mb-4 transition-colors duration-300">
                    <i class="fas fa-user-slash text-4xl text-slate-300 dark:text-slate-600"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 mb-1 transition-colors duration-300">Belum Ada Personel</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 transition-colors duration-300">Tidak ada pengguna dengan izin penugasan yang tersedia.</p>
            </div>
            @endforelse
        </div>

        {{-- Action Bar --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 transition-colors duration-300">
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium flex items-center gap-2 transition-colors duration-300">
                <i class="fas fa-info-circle text-brand-500 dark:text-brand-400"></i> Pastikan shift sudah benar sebelum menyimpan.
            </p>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('locations.index') }}" class="w-full sm:w-auto text-center px-6 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200">
                    <i class="fas fa-save"></i> Simpan Penugasan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection