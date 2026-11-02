@extends('layout.app')

@section('pageTitle', 'Buat Laporan Patroli')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 100%; min-height: 280px; width: 100%; z-index: 10; border-radius: 1rem; }
    
    /* Gaya Kartu Status Kustom */
    .status-option input:checked + .status-card {
        border-color: var(--status-color);
        background-color: var(--status-bg);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px var(--status-shadow), 0 4px 6px -4px var(--status-shadow);
    }
    .status-option input:checked + .status-card .status-icon {
        background-color: var(--status-color);
        color: white;
    }

    /* Override background untuk Dark Mode pada status terpilih */
    .dark .status-option input:checked + .status-card {
        background-color: rgba(255, 255, 255, 0.05);
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-2">
        <a href="{{ route('patrol-reports.index') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Buat Laporan Baru</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Isi kondisi terkini pos jaga beserta bukti foto dan lokasi Anda.</p>
        </div>
    </div>

    @if($locations->isNotEmpty())
    <div class="flex items-start gap-4 p-5 bg-blue-50/50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 rounded-2xl text-blue-800 dark:text-blue-300 transition-colors duration-300">
        <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-900 text-blue-500 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm transition-colors duration-300">
            <i class="fas fa-lightbulb text-lg"></i>
        </div>
        <div class="text-sm">
            <p class="font-bold text-base mb-1 text-blue-900 dark:text-blue-200 transition-colors duration-300">Syarat Pengiriman Laporan</p>
            <ul class="list-disc list-inside text-blue-700/80 dark:text-blue-400/80 space-y-1 ml-1 transition-colors duration-300">
                <li>Anda sudah melakukan <strong>clock-in</strong> dan belum clock-out di pos yang dipilih.</li>
                <li>Waktu pengiriman masih dalam jam kerja shift Anda.</li>
                <li>Jika laporan ditolak, periksa status absensi hari ini di halaman Riwayat Absensi.</li>
            </ul>
        </div>
    </div>
    @endif

    @if ($errors->any() || session('error'))
        <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-700 dark:text-rose-400 text-sm font-bold rounded-2xl px-5 py-4 flex items-center gap-3 shadow-sm transition-colors duration-300">
            <i class="fas fa-exclamation-circle text-xl"></i>
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden transition-colors duration-300">
        <form action="{{ route('patrol-reports.store') }}" method="POST" id="patrolForm">
            @csrf
            <input type="hidden" name="photo" id="photo">
            <input type="hidden" name="lat" id="lat">
            <input type="hidden" name="lng" id="lng">

            <div class="grid grid-cols-1 lg:grid-cols-2">
                
                {{-- SISI KIRI: Informasi Teks --}}
                <div class="p-8 lg:p-10 space-y-8 border-b lg:border-b-0 lg:border-r border-slate-100 dark:border-slate-800 transition-colors duration-300">
                    
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2 transition-colors duration-300">
                            <i class="fas fa-edit text-brand-500 dark:text-brand-400"></i> Detail Laporan
                        </h3>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Pilih Pos Jaga <span class="text-rose-500">*</span></label>
                                <select name="location_id" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all @error('location_id') border-rose-500 @enderror" required>
                                    <option value="">-- Pilih Pos Jaga --</option>
                                    @forelse ($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                    @empty
                                        <option value="" disabled>Anda belum ditugaskan di pos manapun</option>
                                    @endforelse
                                </select>
                                @error('location_id') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3 transition-colors duration-300">Status Kondisi <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="status-option cursor-pointer" style="--status-color:#10b981; --status-bg:#ecfdf5; --status-shadow: rgba(16, 185, 129, 0.2);">
                                        <input type="radio" name="status" value="aman" class="sr-only" required {{ old('status') == 'aman' ? 'checked' : '' }}>
                                        <div class="status-card flex flex-col items-center gap-2 border-2 border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-2 text-center transition-all duration-200">
                                            <div class="status-icon w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center transition-colors">
                                                <i class="fas fa-shield-alt text-lg"></i>
                                            </div>
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 transition-colors duration-300">Aman</span>
                                        </div>
                                    </label>
                                    <label class="status-option cursor-pointer" style="--status-color:#f59e0b; --status-bg:#fffbeb; --status-shadow: rgba(245, 158, 11, 0.2);">
                                        <input type="radio" name="status" value="mencurigakan" class="sr-only" {{ old('status') == 'mencurigakan' ? 'checked' : '' }}>
                                        <div class="status-card flex flex-col items-center gap-2 border-2 border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-2 text-center transition-all duration-200">
                                            <div class="status-icon w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center transition-colors">
                                                <i class="fas fa-eye text-lg"></i>
                                            </div>
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 transition-colors duration-300">Curiga</span>
                                        </div>
                                    </label>
                                    <label class="status-option cursor-pointer" style="--status-color:#ef4444; --status-bg:#fef2f2; --status-shadow: rgba(239, 68, 68, 0.2);">
                                        <input type="radio" name="status" value="temuan_darurat" class="sr-only" {{ old('status') == 'temuan_darurat' ? 'checked' : '' }}>
                                        <div class="status-card flex flex-col items-center gap-2 border-2 border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-2 text-center transition-all duration-200">
                                            <div class="status-icon w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center transition-colors">
                                                <i class="fas fa-triangle-exclamation text-lg"></i>
                                            </div>
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 transition-colors duration-300">Darurat</span>
                                        </div>
                                    </label>
                                </div>
                                @error('status') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Catatan / Kronologi <span class="text-rose-500">*</span></label>
                                <textarea name="report" rows="5" maxlength="1000" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all @error('report') border-rose-500 @enderror" placeholder="Jelaskan hasil pengecekan/patroli Anda di pos ini..." required>{{ old('report') }}</textarea>
                                @error('report') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SISI KANAN: Bukti & Lokasi --}}
                <div class="p-8 lg:p-10 space-y-8 bg-slate-50/50 dark:bg-slate-950/50 transition-colors duration-300">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200 mb-6 flex items-center gap-2 transition-colors duration-300">
                            <i class="fas fa-camera text-brand-500 dark:text-brand-400"></i> Bukti & Lokasi
                        </h3>

                        <div class="space-y-6">
                            {{-- Foto --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Foto Bukti</label>
                                <div id="photoPreviewWrap" class="hidden mb-3 relative rounded-2xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                                    <img id="photoPreview" src="" class="w-full max-h-56 object-cover">
                                </div>
                                <label for="photoInput" class="flex flex-col items-center justify-center gap-3 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl py-8 bg-white dark:bg-slate-900 cursor-pointer hover:bg-brand-50 dark:hover:bg-brand-950/30 hover:border-brand-300 dark:hover:border-brand-700 transition-all group">
                                    <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 group-hover:bg-brand-100 dark:group-hover:bg-brand-900/50 text-slate-400 dark:text-slate-500 group-hover:text-brand-500 dark:group-hover:text-brand-400 flex items-center justify-center transition-colors">
                                        <i class="fas fa-cloud-upload-alt text-2xl"></i>
                                    </div>
                                    <div class="text-center">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-300 block transition-colors duration-300" id="photoLabel">Ambil atau Unggah Foto</span>
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-1 block transition-colors duration-300">Format JPG, PNG (Maks. 5MB)</span>
                                    </div>
                                </label>
                                <input type="file" id="photoInput" accept="image/jpeg,image/jpg,image/png" capture="environment" class="hidden">
                                <p id="photoError" class="text-rose-500 text-xs mt-2 font-semibold hidden"></p>
                                @error('photo') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            {{-- Peta --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 transition-colors duration-300">Lokasi GPS</label>
                                    <span id="gpsStatus" class="text-[11px] font-bold px-2.5 py-1 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg animate-pulse transition-colors duration-300">Mendeteksi...</span>
                                </div>
                                <div class="h-56 bg-slate-200 dark:bg-slate-800 rounded-2xl p-1 relative overflow-hidden shadow-inner border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                                    <div id="map"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-3 mt-3">
                                    <div class="bg-white dark:bg-slate-900 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-center transition-colors duration-300">
                                        <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5 transition-colors duration-300">Latitude</span>
                                        <input type="text" id="latDisplay" class="w-full bg-transparent text-xs font-bold text-slate-700 dark:text-slate-300 text-center outline-none cursor-not-allowed transition-colors duration-300" readonly>
                                    </div>
                                    <div class="bg-white dark:bg-slate-900 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-center transition-colors duration-300">
                                        <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5 transition-colors duration-300">Longitude</span>
                                        <input type="text" id="lngDisplay" class="w-full bg-transparent text-xs font-bold text-slate-700 dark:text-slate-300 text-center outline-none cursor-not-allowed transition-colors duration-300" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Action Buttons Footer --}}
            <div class="px-8 lg:px-10 py-6 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col sm:flex-row items-center justify-between gap-4 transition-colors duration-300">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium transition-colors duration-300"><span class="text-rose-500">*</span> Wajib diisi</p>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('patrol-reports.index') }}" class="w-full sm:w-auto text-center px-6 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" id="submitBtn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200 hover:-translate-y-0.5">
                        <i class="fas fa-paper-plane"></i> Kirim Laporan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ===== 1. Peta & Deteksi Lokasi GPS =====
        let defaultLat = 1.1186; 
        let defaultLng = 104.0485;

        let map = L.map('map').setView([defaultLat, defaultLng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);

        let marker = L.marker([defaultLat, defaultLng]).addTo(map);
        let gpsStatus = document.getElementById('gpsStatus');

        function setCoordinates(lat, lng) {
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
            document.getElementById('latDisplay').value = lat.toFixed(6);
            document.getElementById('lngDisplay').value = lng.toFixed(6);
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 17);
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    setCoordinates(pos.coords.latitude, pos.coords.longitude);
                    gpsStatus.textContent = 'GPS Terkunci';
                    gpsStatus.className = 'text-[11px] font-bold px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-lg transition-colors duration-300';
                },
                function () {
                    gpsStatus.textContent = 'GPS Gagal';
                    gpsStatus.className = 'text-[11px] font-bold px-2.5 py-1 bg-rose-100 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 rounded-lg transition-colors duration-300';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            gpsStatus.textContent = 'GPS Tidak Didukung';
            gpsStatus.className = 'text-[11px] font-bold px-2.5 py-1 bg-rose-100 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 rounded-lg transition-colors duration-300';
        }

        // ===== 2. Ambil / Unggah Foto -> Base64 =====
        const photoInput = document.getElementById('photoInput');
        const photoField = document.getElementById('photo');
        const photoPreview = document.getElementById('photoPreview');
        const photoPreviewWrap = document.getElementById('photoPreviewWrap');
        const photoLabel = document.getElementById('photoLabel');
        const photoError = document.getElementById('photoError');
        const maxSize = 5 * 1024 * 1024; 

        photoInput.addEventListener('change', function (e) {
            photoError.classList.add('hidden');
            const file = e.target.files[0];
            if (!file) return;

            const allowed = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowed.includes(file.type)) {
                photoError.textContent = 'Foto harus berformat JPEG atau PNG.';
                photoError.classList.remove('hidden');
                photoInput.value = '';
                return;
            }
            if (file.size > maxSize) {
                photoError.textContent = 'Ukuran foto maksimal 5MB.';
                photoError.classList.remove('hidden');
                photoInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (ev) {
                photoField.value = ev.target.result; 
                photoPreview.src = ev.target.result;
                photoPreviewWrap.classList.remove('hidden');
                photoLabel.textContent = 'Ganti Foto';
            };
            reader.onerror = function () {
                photoError.textContent = 'Gagal membaca file foto.';
                photoError.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });

        // ===== 3. Validasi ringan sebelum submit =====
        document.getElementById('patrolForm').addEventListener('submit', function () {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
        });
    });
</script>
@endpush