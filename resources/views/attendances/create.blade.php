@extends('layout.app')

@section('pageTitle', 'Absensi')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<style>
    #map { height: 100%; min-height: 280px; width: 100%; border-radius: 1rem; z-index: 0; }
    #camera-preview, #photo-canvas { border-radius: 1rem; object-fit: cover; }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">

    {{-- Header Halaman --}}
    <div class="text-center sm:text-left">
        <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Terminal Absensi</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium transition-colors duration-300">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-2xl text-rose-800 dark:text-rose-400 shadow-sm transition-colors duration-300">
        <i class="fas fa-exclamation-circle text-xl text-rose-500 dark:text-rose-400"></i>
        <p class="text-sm font-bold">{{ session('error') }}</p>
    </div>
    @endif
    @if($errors->any())
    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-2xl shadow-sm transition-colors duration-300">
        <ul class="list-disc list-inside text-sm text-rose-600 dark:text-rose-400 space-y-1 font-medium ml-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- STATE 1: SUDAH ABSEN LENGKAP --}}
    @if($todayAttendance && $todayAttendance->clock_in_at && $todayAttendance->clock_out_at)
    @php
        $statusStyle = match($todayAttendance->status) {
            'hadir' => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900/50',
            'terlambat' => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-900/50',
            'pulang_cepat' => 'bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-900/50',
            'izin' => 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-900/50',
            'alfa' => 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-900/50',
            default => 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700',
        };
        $statusLabel = match($todayAttendance->status) {
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'pulang_cepat' => 'Pulang Cepat',
            'izin' => 'Izin',
            'alfa' => 'Alfa',
            default => 'Belum Hadir',
        };
    @endphp
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm p-10 text-center flex flex-col items-center transition-colors duration-300">
        <div class="w-24 h-24 bg-emerald-50 dark:bg-emerald-950/50 rounded-full flex items-center justify-center mb-6 transition-colors duration-300">
            <i class="fas fa-check-circle text-5xl text-emerald-500 dark:text-emerald-400"></i>
        </div>
        <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-2 transition-colors duration-300">Absensi Selesai</h3>
        <p class="text-slate-500 dark:text-slate-400 font-medium mb-6 transition-colors duration-300">
            Shift Anda hari ini telah tercatat dengan aman.
        </p>

        <div class="flex items-center gap-6 bg-slate-50 dark:bg-slate-800/50 px-6 py-4 rounded-2xl border border-slate-100 dark:border-slate-800 mb-6 w-full max-w-sm justify-center transition-colors duration-300">
            <div class="text-center">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 transition-colors duration-300">Masuk</p>
                <p class="text-xl font-black text-slate-800 dark:text-white transition-colors duration-300">{{ \Carbon\Carbon::parse($todayAttendance->clock_in_at)->format('H:i') }}</p>
            </div>
            <div class="w-px h-10 bg-slate-200 dark:bg-slate-700 transition-colors duration-300"></div>
            <div class="text-center">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 transition-colors duration-300">Keluar</p>
                <p class="text-xl font-black text-slate-800 dark:text-white transition-colors duration-300">{{ \Carbon\Carbon::parse($todayAttendance->clock_out_at)->format('H:i') }}</p>
            </div>
        </div>

        <span class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-xl border {{ $statusStyle }} mb-6 transition-colors duration-300">
            Status: {{ $statusLabel }}
        </span>

        @if($todayAttendance->notes)
        <div class="flex items-start gap-3 p-4 mb-8 bg-blue-50/50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 rounded-2xl text-left w-full max-w-lg transition-colors duration-300">
            <i class="fas fa-info-circle text-blue-500 dark:text-blue-400 mt-0.5"></i>
            <p class="text-sm text-blue-800 dark:text-blue-300 font-medium transition-colors duration-300">{{ $todayAttendance->notes }}</p>
        </div>
        @endif

        <a href="{{ route('attendances.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 dark:bg-brand-600 hover:bg-slate-800 dark:hover:bg-brand-500 text-white text-sm font-bold rounded-xl transition-all shadow-md dark:shadow-brand-900/50">
            Lihat Riwayat Absensi <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    {{-- STATE 2: BELUM DITUGASKAN --}}
    @elseif($locations->isEmpty())
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm p-10 text-center flex flex-col items-center transition-colors duration-300">
        <div class="w-24 h-24 bg-amber-50 dark:bg-amber-950/50 rounded-full flex items-center justify-center mb-6 transition-colors duration-300">
            <i class="fas fa-map-location-dot text-5xl text-amber-500 dark:text-amber-400"></i>
        </div>
        <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-2 transition-colors duration-300">Belum Ada Penugasan</h3>
        <p class="text-slate-500 dark:text-slate-400 font-medium max-w-md mx-auto transition-colors duration-300">Anda belum ditugaskan ke pos jaga manapun saat ini. Silakan hubungi Admin untuk mendapatkan penugasan shift.</p>
    </div>

    {{-- STATE 3: FORM CLOCK IN / OUT --}}
    @else
    @php
        $mode = ($todayAttendance && $todayAttendance->clock_in_at && !$todayAttendance->clock_out_at) ? 'out' : 'in';
        $formAction = $mode === 'in' ? route('attendances.clock-in') : route('attendances.clock-out', $todayAttendance->id);
    @endphp

    <div x-data="attendanceForm()" x-init="init()">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden flex flex-col transition-colors duration-300">
            
            {{-- Card Header: Mode Absensi --}}
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 flex flex-col sm:flex-row items-center justify-between gap-4 transition-colors duration-300">
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors duration-300 {{ $mode === 'in' ? 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400' : 'bg-purple-100 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400' }}">
                        <i class="fas {{ $mode === 'in' ? 'fa-right-to-bracket' : 'fa-right-from-bracket' }} text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-800 dark:text-white transition-colors duration-300">
                            {{ $mode === 'in' ? 'Clock In (Masuk)' : 'Clock Out (Keluar)' }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5 transition-colors duration-300">Validasi Keamanan Ganda: GPS & Foto</p>
                    </div>
                </div>
                <div class="hidden sm:block">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[11px] font-bold rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm transition-colors duration-300">
                        <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span> Sistem Siap
                    </span>
                </div>
            </div>

            <form action="{{ $formAction }}" method="POST" @submit="!canSubmit() && $event.preventDefault()">
                @csrf

                <div class="p-6 space-y-6">
                    {{-- Pilih Pos (Hanya saat Clock In) --}}
                    @if($mode === 'in')
                    <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 sm:p-5 transition-colors duration-300">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Pilih Pos Jaga Anda <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors duration-300">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <select name="location_id" x-model="selectedLocationId" @change="updateSelectedLocation()" required
                                    class="w-full pl-11 pr-4 py-3 text-sm font-bold border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 bg-white dark:bg-slate-800 text-slate-800 dark:text-white appearance-none cursor-pointer transition-all shadow-sm">
                                <option value="">-- Ketuk untuk memilih pos --</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-lat="{{ $loc->lat }}" data-lng="{{ $loc->lng }}" data-radius="{{ $loc->radius }}">
                                    {{ $loc->name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors duration-300">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Split View: Peta & Kamera --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        {{-- Kiri: Peta --}}
                        <div class="flex flex-col">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-location-crosshairs text-brand-500 dark:text-brand-400"></i> Lokasi GPS
                                </label>
                                <button type="button" @click="getLocation()" class="text-[11px] font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 bg-brand-50 dark:bg-brand-950/50 hover:bg-brand-100 dark:hover:bg-brand-900/50 px-3 py-1.5 rounded-lg transition-colors border border-brand-100 dark:border-brand-900/50">
                                    <i class="fas fa-rotate-right mr-1"></i> Refresh
                                </button>
                            </div>

                            <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-1 relative overflow-hidden min-h-[280px] shadow-inner transition-colors duration-300">
                                <div id="map"></div>
                            </div>

                            <div class="mt-3 p-3 rounded-xl border transition-colors duration-300" 
                                 :class="gpsStatus.ok ? 'bg-emerald-50 dark:bg-emerald-950/50 border-emerald-200 dark:border-emerald-900/50' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700'">
                                <p class="text-[11px] font-bold flex items-center gap-2 transition-colors duration-300" :class="gpsStatus.ok ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'">
                                    <i class="fas" :class="gpsStatus.ok ? 'fa-check-circle' : 'fa-circle-notch fa-spin'"></i>
                                    <span x-text="gpsStatus.message"></span>
                                </p>
                            </div>

                            <input type="hidden" name="lat" x-model="lat">
                            <input type="hidden" name="lng" x-model="lng">
                        </div>

                        {{-- Kanan: Kamera --}}
                        <div class="flex flex-col">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-camera text-brand-500 dark:text-brand-400"></i> Foto Bukti
                                </label>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-md transition-colors duration-300">Wajib Selfie</span>
                            </div>

                            <p x-show="cameraError" x-text="cameraError" class="text-xs text-rose-600 dark:text-rose-400 font-bold mb-2 transition-colors duration-300"></p>

                            <div class="flex-1 flex flex-col justify-between">
                                {{-- Preview Kamera --}}
                                <div x-show="!photoTaken" class="flex flex-col h-full justify-between space-y-3">
                                    <div class="flex-1 relative rounded-2xl overflow-hidden bg-slate-900 border-2 border-slate-200 dark:border-slate-700 shadow-inner aspect-video min-h-[200px] transition-colors duration-300">
                                        <video id="camera-preview" autoplay playsinline muted class="w-full h-full object-cover"></video>
                                    </div>
                                    <button type="button" @click="takePhoto()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-sm font-bold rounded-xl transition-all shadow-md">
                                        <i class="fas fa-camera"></i> Jepret Foto
                                    </button>
                                </div>

                                {{-- Hasil Foto --}}
                                <div x-show="photoTaken" x-cloak class="flex flex-col h-full justify-between space-y-3">
                                    <div class="flex-1 relative rounded-2xl overflow-hidden border-2 border-emerald-400 dark:border-emerald-600 shadow-inner aspect-video min-h-[200px] transition-colors duration-300">
                                        <canvas id="photo-canvas" class="w-full h-full object-cover bg-slate-100 dark:bg-slate-800"></canvas>
                                    </div>
                                    <button type="button" @click="retakePhoto()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-all">
                                        <i class="fas fa-rotate-left"></i> Ambil Ulang Foto
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="photo" id="photo-input" required>
                        </div>
                    </div>
                </div>

                {{-- Card Footer: Tombol Aksi (Proporsional) --}}
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 transition-colors duration-300">
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 transition-colors duration-300">
                        <i class="fas fa-lock text-brand-500 dark:text-brand-400"></i>
                        <span>Data lokasi dan foto dilindungi enkripsi.</span>
                    </div>
                    <button type="submit" :disabled="!canSubmit()"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 text-white text-sm font-bold rounded-xl shadow-md transition-all duration-200
                                   disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none
                                   {{ $mode === 'in' ? 'bg-emerald-600 hover:bg-emerald-700 hover:-translate-y-0.5 shadow-emerald-500/30 dark:shadow-emerald-900/50' : 'bg-purple-600 hover:bg-purple-700 hover:-translate-y-0.5 shadow-purple-500/30 dark:shadow-purple-900/50' }}">
                        <i class="fas {{ $mode === 'in' ? 'fa-fingerprint' : 'fa-check-double' }}"></i>
                        {{ $mode === 'in' ? 'Konfirmasi Clock In' : 'Konfirmasi Clock Out' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    function attendanceForm() {
        return {
            map: null, marker: null, radiusCircle: null, lat: null, lng: null, selectedLocationId: '', selectedLocation: null,
            gpsStatus: { ok: false, message: 'Menunggu lokasi GPS...' },
            photoTaken: false, stream: null, cameraError: null,

            init() {
                if (this.map) return;
                this.map = L.map('map').setView([-0.789275, 113.921327], 5);

                // Tile OpenStreetMap standar (sama untuk light & dark mode, tanpa API key)
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(this.map);

                // Hitung ulang ukuran peta setelah layout selesai
                setTimeout(() => this.map.invalidateSize(), 200);

                this.startCamera();
                this.getLocation();
            },
            updateSelectedLocation() {
                const select = document.querySelector('select[name="location_id"]');
                if(!select) return;
                const opt = select.options[select.selectedIndex];
                if (opt && opt.dataset.lat) {
                    this.selectedLocation = { lat: parseFloat(opt.dataset.lat), lng: parseFloat(opt.dataset.lng), radius: parseFloat(opt.dataset.radius) };
                    this.drawLocationCircle();
                } else { this.selectedLocation = null; }
            },
            drawLocationCircle() {
                if (this.radiusCircle) this.map.removeLayer(this.radiusCircle);
                if (!this.selectedLocation) return;
                this.radiusCircle = L.circle([this.selectedLocation.lat, this.selectedLocation.lng], {
                    radius: this.selectedLocation.radius, color: '#10b981', fillColor: '#10b981', fillOpacity: 0.15
                }).addTo(this.map);
                this.map.setView([this.selectedLocation.lat, this.selectedLocation.lng], 17);
            },
            getLocation() {
                if (!navigator.geolocation) {
                    this.gpsStatus = { ok: false, message: 'Perangkat tidak mendukung GPS.' }; return;
                }
                this.gpsStatus = { ok: false, message: 'Mengambil lokasi GPS...' };
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.lat = pos.coords.latitude; this.lng = pos.coords.longitude;
                        this.gpsStatus = { ok: true, message: `Lokasi ditemukan (akurasi ${Math.round(pos.coords.accuracy)}m)` };
                        if (this.marker) this.map.removeLayer(this.marker);
                        this.marker = L.marker([this.lat, this.lng]).addTo(this.map).bindPopup('Posisi Anda').openPopup();
                        if (!this.selectedLocation) { this.map.setView([this.lat, this.lng], 17); }
                    },
                    (err) => { this.gpsStatus = { ok: false, message: 'Gagal mengambil lokasi: ' + err.message }; },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            },
            async startCamera() {
                this.cameraError = null;
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                    document.getElementById('camera-preview').srcObject = this.stream;
                } catch (err) {
                    this.cameraError = 'Gagal mengakses kamera: ' + err.message + '. Pastikan izin aktif.';
                }
            },
            takePhoto() {
                const video = document.getElementById('camera-preview');
                if (!video.videoWidth) { this.cameraError = 'Kamera belum siap.'; return; }
                this.cameraError = null;
                const canvas = document.getElementById('photo-canvas');
                canvas.width = video.videoWidth; canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                document.getElementById('photo-input').value = canvas.toDataURL('image/jpeg', 0.85);
                this.photoTaken = true;
                if (this.stream) { this.stream.getTracks().forEach(track => track.stop()); }
            },
            retakePhoto() {
                this.photoTaken = false;
                document.getElementById('photo-input').value = '';
                this.startCamera();
            },
            canSubmit() { return !!(this.lat && this.lng && this.photoTaken); }
        }
    }
</script>
@endpush