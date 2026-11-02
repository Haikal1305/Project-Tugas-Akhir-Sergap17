@extends('layout.app')

@section('pageTitle', 'Pelaporan')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header Section --}}
    <div class="text-center max-w-2xl mx-auto mb-10 mt-4">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-brand-100 dark:bg-brand-950/50 text-brand-600 dark:text-brand-400 mb-4 shadow-sm transition-colors duration-300">
            <i class="fas fa-file-export text-2xl"></i>
        </div>
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white transition-colors duration-300">Pusat Pelaporan</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 transition-colors duration-300">Export data laporan patroli ke dalam format Excel atau PDF berdasarkan Pos Jaga dan rentang tanggal tertentu.</p>
    </div>

    {{-- Alert Success / Error --}}
    @if(session('success'))
    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-400 rounded-2xl text-sm font-bold flex items-center gap-3 shadow-sm transition-colors duration-300">
        <i class="fas fa-check-circle text-xl text-emerald-500 dark:text-emerald-400"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-400 rounded-2xl text-sm font-bold flex items-center gap-3 shadow-sm transition-colors duration-300">
        <i class="fas fa-exclamation-circle text-xl text-rose-500 dark:text-rose-400"></i> {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-400 rounded-2xl text-sm font-bold flex items-center gap-3 shadow-sm transition-colors duration-300">
        <i class="fas fa-exclamation-circle text-xl text-rose-500 dark:text-rose-400"></i>
        <div>
            <p>Periksa kembali input Anda:</p>
            <ul class="list-disc list-inside ml-1 font-medium mt-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 overflow-hidden transition-colors duration-300">
        <form id="pelaporanForm" action="{{ route('pelaporan.export.excel') }}" method="GET">

            {{-- Bagian Atas: Filter --}}
            <div class="p-8 lg:p-10 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 transition-colors duration-300">
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-filter text-brand-500 dark:text-brand-400"></i> Parameter Laporan
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Pos Jaga --}}
                    <div>
                        <label for="location_id" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300">
                            Pos Jaga
                        </label>
                        <div class="relative">
                            <select id="location_id" name="location_id" class="w-full pl-4 pr-10 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all appearance-none shadow-sm cursor-pointer">
                                <option value="">-- Semua Pos --</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div>
                        <label for="date_from" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300">
                            Dari Tanggal
                        </label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all shadow-sm cursor-pointer">
                    </div>

                    {{-- Tanggal Akhir --}}
                    <div>
                        <label for="date_to" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2 transition-colors duration-300">
                            Sampai Tanggal
                        </label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all shadow-sm cursor-pointer">
                    </div>
                </div>
            </div>

            {{-- Bagian Bawah: Pilihan Format Export --}}
            <div class="p-8 lg:p-10 bg-white dark:bg-slate-900 transition-colors duration-300">
                <h2 class="text-lg font-bold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-download text-brand-500 dark:text-brand-400"></i> Pilih Format Export
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Card Excel --}}
                    <button type="submit" formaction="{{ route('pelaporan.export.excel') }}" class="group relative flex flex-col items-start p-6 bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 rounded-2xl hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-all duration-300 text-left shadow-sm hover:shadow-md hover:-translate-y-1">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-excel text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 mb-1 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors duration-300">Export ke Excel</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed transition-colors duration-300">Format .xlsx cocok untuk pengolahan data lebih lanjut (pivot, filter, dll).</p>
                    </button>

                    {{-- Card PDF --}}
                    <button type="submit" formaction="{{ route('pelaporan.export.pdf') }}" class="group relative flex flex-col items-start p-6 bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 rounded-2xl hover:border-rose-500 dark:hover:border-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-all duration-300 text-left shadow-sm hover:shadow-md hover:-translate-y-1">
                        <div class="w-12 h-12 rounded-xl bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-pdf text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 mb-1 group-hover:text-rose-700 dark:group-hover:text-rose-400 transition-colors duration-300">Export ke PDF</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed transition-colors duration-300">Format .pdf cocok untuk dicetak atau diarsipkan sebagai dokumen resmi.</p>
                    </button>
                </div>

                {{-- Tombol Reset --}}
                <div class="mt-8 text-center border-t border-slate-100 dark:border-slate-800 pt-6 transition-colors duration-300">
                    <button type="button" onclick="document.getElementById('pelaporanForm').reset()" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-colors">
                        <i class="fas fa-rotate-left"></i> Reset Parameter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Info tambahan --}}
    <div class="bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 text-slate-800 dark:text-white relative overflow-hidden shadow-sm dark:shadow-slate-950/50 transition-colors duration-300">
        {{-- Efek blur disesuaikan opasitasnya untuk light mode --}}
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-brand-500/10 dark:bg-brand-500/20 rounded-full blur-2xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center gap-6">
            {{-- Ikon Box --}}
            <div class="w-14 h-14 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-700 shadow-sm">
                <i class="fas fa-shield-alt text-2xl text-brand-600 dark:text-brand-400"></i>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-2 text-slate-900 dark:text-white transition-colors duration-300">Sistem Keamanan ReBAC</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed transition-colors duration-300">
                    Sistem ini dilindungi oleh <strong class="text-brand-600 dark:text-brand-400">Relationship-Based Access Control</strong>. Anda hanya dapat mengekspor data dari pos jaga yang secara eksplisit telah diberikan hak akses <em>(export)</em> kepada Anda. Jika Anda mencoba mengakses pos yang tidak diizinkan, sistem akan menolak permintaan tersebut <em>(403 Forbidden)</em>.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection