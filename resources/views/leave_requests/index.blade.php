@extends('layout.app')

@section('pageTitle', 'Manajemen Pengajuan Izin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white transition-colors duration-300">Manajemen Izin</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium transition-colors duration-300">
                @can('leave-request view all')
                    Kelola dan tinjau pengajuan izin seluruh personel.
                @else
                    Ajukan izin ketidakhadiran dan pantau status pengajuan Anda.
                @endcan
            </p>
        </div>

        {{-- Tombol Trigger Modal Form --}}
        @can('leave-request create')
        <button onclick="openModal('createLeaveModal')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white font-bold text-sm rounded-xl hover:bg-brand-700 transition-all shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 hover:-translate-y-0.5">
            <i class="fas fa-plus"></i> Ajukan Izin Baru
        </button>
        @endcan
    </div>

    {{-- Info Banner --}}
    <div class="flex items-start gap-4 p-5 bg-blue-50/70 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 rounded-3xl text-blue-800 dark:text-blue-300 shadow-sm transition-colors duration-300">
        <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-900 text-blue-500 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm transition-colors duration-300">
            <i class="fas fa-info-circle text-lg"></i>
        </div>
        <div class="text-sm">
            @can('leave-request view all')
                <p class="font-bold text-base mb-1 text-blue-900 dark:text-blue-200 transition-colors duration-300">Akses Global Admin</p>
                <p class="text-blue-700/80 dark:text-blue-400/80 transition-colors duration-300">Anda melihat pengajuan izin dari seluruh personel di semua pos. Tinjau pengajuan dengan bijak.</p>
            @else
                <p class="font-bold text-base mb-1 text-blue-900 dark:text-blue-200 transition-colors duration-300">Akses Personal</p>
                <p class="text-blue-700/80 dark:text-blue-400/80 transition-colors duration-300">Anda hanya melihat pengajuan izin milik Anda sendiri. Keputusan (Setujui/Tolak) akan dilakukan oleh Admin.</p>
            @endcan
        </div>
    </div>

    {{-- Alert Success / Error --}}
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
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-exclamation-circle text-rose-600 dark:text-rose-400 text-lg"></i>
            <p class="text-sm font-bold text-rose-800 dark:text-rose-400">Terjadi kesalahan:</p>
        </div>
        <ul class="list-disc list-inside text-sm text-rose-600 dark:text-rose-400 space-y-1 font-medium ml-1 transition-colors duration-300">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- DAFTAR KARTU PENGAJUAN IZIN --}}
    <div class="space-y-4">
        @forelse($leaveRequests as $leave)
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm hover:shadow-md dark:hover:shadow-slate-950/50 transition-all duration-300 overflow-hidden group">
            <div class="flex flex-col lg:flex-row">
                
                {{-- Sisi Kiri: Info Personel & Tanggal --}}
                <div class="p-6 lg:w-72 bg-slate-50/50 dark:bg-slate-950/50 border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-slate-800 shrink-0 flex flex-col justify-center gap-4 transition-colors duration-300">
                    @can('leave-request view all')
                    <div class="flex items-center gap-3">
                        {{-- Avatar dengan pengecekan profil --}}
                        <div class="w-10 h-10 rounded-full bg-slate-900 dark:bg-slate-800 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0 overflow-hidden transition-colors duration-300">
                            @if($leave->user && $leave->user->profile)
                                <img src="{{ asset('storage/' . $leave->user->profile) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($leave->user->name ?? 'U', 0, 1)) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 dark:text-slate-200 text-sm leading-tight truncate transition-colors duration-300">{{ $leave->user->name ?? '-' }}</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium transition-colors duration-300">Personel</p>
                        </div>
                    </div>
                    @endcan

                    <div>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 transition-colors duration-300">Tanggal Izin</p>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm font-bold text-sm transition-colors duration-300">
                            <i class="fas fa-calendar-day text-brand-500 dark:text-brand-400"></i>
                            {{ \Carbon\Carbon::parse($leave->date)->translatedFormat('d F Y') }}
                        </div>
                    </div>
                </div>

                {{-- Tengah: Alasan & Catatan --}}
                <div class="p-6 flex-1 flex flex-col justify-center min-w-0 transition-colors duration-300">
                    <div class="mb-3">
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 transition-colors duration-300">Alasan Pengajuan</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300 font-medium leading-relaxed transition-colors duration-300">"{{ $leave->reason }}"</p>
                    </div>
                    
                    @if($leave->admin_note)
                    <div class="p-3 bg-blue-50/50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 rounded-xl mt-2 transition-colors duration-300">
                        <p class="text-[10px] font-bold text-blue-500 dark:text-blue-400 uppercase tracking-wider mb-1 flex items-center gap-1 transition-colors duration-300">
                            <i class="fas fa-comment-dots"></i> Catatan Admin
                        </p>
                        <p class="text-xs text-blue-800 dark:text-blue-300 transition-colors duration-300">{{ $leave->admin_note }}</p>
                    </div>
                    @endif
                </div>

                {{-- Kanan: Status, Bukti & Aksi --}}
                <div class="p-6 lg:w-64 border-t lg:border-t-0 lg:border-l border-slate-100 dark:border-slate-800 flex flex-row lg:flex-col justify-between lg:justify-center items-center lg:items-end gap-4 shrink-0 bg-slate-50/30 dark:bg-slate-950/30 transition-colors duration-300">
                    
                    {{-- Status Badge --}}
                    <div>
                        @if($leave->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50 shadow-sm transition-colors duration-300">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Menunggu Review
                            </span>
                        @elseif($leave->status === 'disetujui')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50 shadow-sm transition-colors duration-300">
                                <i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400"></i> Disetujui
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 shadow-sm transition-colors duration-300">
                                <i class="fas fa-times-circle text-rose-500 dark:text-rose-400"></i> Ditolak
                            </span>
                        @endif
                    </div>

                    {{-- Bukti & Tombol Aksi --}}
                    <div class="flex items-center gap-2">
                        {{-- Bukti Lampiran --}}
                        @if($leave->attachment)
                            <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900/50 hover:bg-indigo-600 dark:hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Lihat Bukti">
                                <i class="fas fa-paperclip text-sm"></i>
                            </a>
                        @endif

                        {{-- Tombol Review --}}
                        @can('leave-request review')
                            @if($leave->status === 'pending')
                                <button onclick="openReviewModal('{{ $leave->id }}')" class="h-9 px-3 rounded-xl bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-400 hover:bg-brand-600 dark:hover:bg-brand-600 hover:text-white flex items-center justify-center border border-brand-200 dark:border-brand-900/50 transition-all shadow-sm font-bold text-xs gap-1.5" title="Review Pengajuan">
                                    <i class="fas fa-clipboard-check"></i> Review
                                </button>
                            @endif
                        @endcan

                        {{-- Tombol Hapus --}}
                        @can('leave-request delete')
                            <form action="{{ route('leave-requests.destroy', $leave->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data izin ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-xl text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 flex items-center justify-center transition-colors" title="Hapus Data">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @empty
        {{-- Empty State --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm py-24 text-center flex flex-col items-center transition-colors duration-300">
            <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mb-4 border border-slate-100 dark:border-slate-800 transition-colors duration-300">
                <i class="fas fa-envelope-open-text text-4xl text-slate-300 dark:text-slate-600"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 mb-1 transition-colors duration-300">Belum Ada Pengajuan</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto transition-colors duration-300">Saat ini tidak ada data pengajuan izin yang perlu ditampilkan.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if(method_exists($leaveRequests, 'hasPages') && $leaveRequests->hasPages())
    <div class="mt-6">
        {{ $leaveRequests->links() }}
    </div>
    @endif
</div>

{{-- MODAL: Ajukan Izin Baru (Modern Glassmorphism) --}}
@can('leave-request create')
<div id="createLeaveModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-colors duration-300">
    <div class="bg-white dark:bg-slate-900 border border-transparent dark:border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200 transition-colors">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white transition-colors duration-300">Ajukan Izin</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Isi form di bawah dengan lengkap.</p>
            </div>
            <button onclick="closeModal('createLeaveModal')" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-slate-700 dark:text-slate-300 text-xs font-bold uppercase tracking-wider mb-2 transition-colors duration-300">Tanggal Izin <span class="text-rose-500">*</span></label>
                <input type="date" name="date" class="w-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl px-4 py-3 text-sm font-medium focus:bg-white dark:focus:bg-slate-800/80 focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all" required>
            </div>

            <div>
                <label class="block text-slate-700 dark:text-slate-300 text-xs font-bold uppercase tracking-wider mb-2 transition-colors duration-300">Alasan Izin <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="3" class="w-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl px-4 py-3 text-sm font-medium focus:bg-white dark:focus:bg-slate-800/80 focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" placeholder="Tuliskan alasan lengkap..." required></textarea>
            </div>

            <div>
                <label class="block text-slate-700 dark:text-slate-300 text-xs font-bold uppercase tracking-wider mb-2 transition-colors duration-300">Bukti Lampiran <span class="text-slate-400 dark:text-slate-500 font-normal">(Opsional)</span></label>
                <input type="file" name="attachment" accept="image/png, image/jpeg, image/jpg" class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 dark:file:bg-brand-950/50 file:text-brand-700 dark:file:text-brand-400 hover:file:bg-brand-100 dark:hover:file:bg-brand-900/50 transition-all cursor-pointer border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 p-1">
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 font-medium transition-colors duration-300">Format: JPG, PNG. Maks: 2MB.</p>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
                <button type="button" onclick="closeModal('createLeaveModal')" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-bold transition-colors shadow-md shadow-brand-500/20 dark:shadow-brand-900/50">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- MODAL: Review Izin (Modern Glassmorphism) --}}
@can('leave-request review')
<div id="reviewLeaveModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-colors duration-300">
    <div class="bg-white dark:bg-slate-900 border border-transparent dark:border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200 transition-colors">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white transition-colors duration-300">Review Pengajuan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Tentukan keputusan untuk izin ini.</p>
            </div>
            <button onclick="closeModal('reviewLeaveModal')" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="reviewForm" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-slate-700 dark:text-slate-300 text-xs font-bold uppercase tracking-wider mb-2 transition-colors duration-300">Status Keputusan <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <select name="status" class="w-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 dark:text-white appearance-none focus:bg-white dark:focus:bg-slate-800/80 focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all cursor-pointer" required>
                        <option value="disetujui">✅ Setujui Pengajuan</option>
                        <option value="ditolak">❌ Tolak Pengajuan</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors duration-300">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-slate-700 dark:text-slate-300 text-xs font-bold uppercase tracking-wider mb-2 transition-colors duration-300">Catatan Admin <span class="text-slate-400 dark:text-slate-500 font-normal">(Opsional)</span></label>
                <textarea name="admin_note" rows="3" class="w-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl px-4 py-3 text-sm font-medium focus:bg-white dark:focus:bg-slate-800/80 focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" placeholder="Berikan alasan penolakan atau catatan tambahan..."></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
                <button type="button" onclick="closeModal('reviewLeaveModal')" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-slate-900 dark:bg-brand-600 hover:bg-slate-800 dark:hover:bg-brand-500 text-white rounded-xl text-sm font-bold transition-colors shadow-md dark:shadow-brand-900/50">Simpan Review</button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- JavaScript Bawaan --}}
<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function openReviewModal(leaveId) {
        const form = document.getElementById('reviewForm');
        form.action = `/leave-requests/${leaveId}/review`;
        openModal('reviewLeaveModal');
    }
</script>
@endsection