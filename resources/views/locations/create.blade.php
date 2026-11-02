@extends('layout.app')

@section('pageTitle', 'Tambah Pos Jaga Baru')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 100%; min-height: 450px; width: 100%; z-index: 10; border-radius: 1rem; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    {{-- Header Section --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('locations.index') }}" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-brand-600 dark:hover:text-brand-400 transition shadow-sm shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight transition-colors duration-300">Tambah Pos Jaga</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 transition-colors duration-300">Tentukan titik kordinat dan detail informasi lokasi penugasan baru.</p>
        </div>
    </div>

    {{-- Form Section --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm p-6 lg:p-8 transition-colors duration-300">
        <form action="{{ route('locations.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10">
                
                {{-- Kolom Kiri: Input Form --}}
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Nama Pos <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all @error('name') border-rose-500 @enderror" value="{{ old('name') }}" required placeholder="Contoh: Gerbang Utama">
                        @error('name') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Alamat / Deskripsi</label>
                        <textarea id="address" name="address" rows="3" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all" placeholder="Geser pin di peta untuk mengisi alamat otomatis, atau ketik manual...">{{ old('address') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1 transition-colors duration-300">Radius Toleransi (Meter) <span class="text-rose-500">*</span></label>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-2 transition-colors duration-300">Jarak maksimal satpam bisa melakukan Clock In/Out dari titik pos.</p>
                        <div class="relative">
                            <input type="number" id="radius" name="radius" class="w-full pl-4 pr-12 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800/80 focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 transition-all" value="{{ old('radius', 50) }}" min="10" required>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 dark:text-slate-500 transition-colors duration-300">m</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Latitude <span class="text-rose-500">*</span></label>
                            <input type="text" id="lat" name="lat" class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-500 dark:text-slate-400 cursor-not-allowed transition-colors duration-300" value="{{ old('lat') }}" readonly required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 transition-colors duration-300">Longitude <span class="text-rose-500">*</span></label>
                            <input type="text" id="lng" name="lng" class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-500 dark:text-slate-400 cursor-not-allowed transition-colors duration-300" value="{{ old('lng') }}" readonly required>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Peta Interaktif --}}
                <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 transition-colors duration-300">Tentukan Titik Kordinat</label>
                        <span class="text-[10px] font-bold px-2.5 py-1 bg-brand-50 dark:bg-brand-950/50 text-brand-600 dark:text-brand-400 rounded-md border border-brand-100 dark:border-brand-900/50 uppercase tracking-wider transition-colors duration-300">Interaktif</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3 transition-colors duration-300">Klik pada peta atau geser pin untuk menentukan lokasi pos yang presisi.</p>
                    
                    <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-1 relative overflow-hidden shadow-inner transition-colors duration-300">
                        <div id="map"></div>
                    </div>
                </div>

            </div>

            {{-- Action Buttons --}}
            <div class="mt-10 pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 transition-colors duration-300">
                <a href="{{ route('locations.index') }}" class="px-6 py-3 bg-slate-100 dark:bg-slate-800 border border-transparent hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md shadow-brand-500/20 dark:shadow-brand-900/50 transition-all duration-200 hover:-translate-y-0.5">
                    <i class="fas fa-save"></i> Simpan Pos Jaga
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let defaultLat = 1.1186; 
        let defaultLng = 104.0485;
        let radiusInput = document.getElementById('radius');
        let addressInput = document.getElementById('address');
        let currentRadius = radiusInput.value || 50;

        let map = L.map('map').setView([defaultLat, defaultLng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Hitung ulang ukuran peta setelah layout selesai
        setTimeout(() => map.invalidateSize(), 200);

        let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
        let circle = L.circle([defaultLat, defaultLng], {
            color: '#059669',
            fillColor: '#10b981',
            fillOpacity: 0.2,
            radius: currentRadius
        }).addTo(map);

        function updateCoordinates(lat, lng) {
            document.getElementById('lat').value = lat.toFixed(6);
            document.getElementById('lng').value = lng.toFixed(6);
        }

        async function fetchAddress(lat, lng) {
            addressInput.value = "Mencari alamat...";
            try {
                let response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=id`);
                let data = await response.json();
                if (data && data.display_name) {
                    addressInput.value = data.display_name;
                } else {
                    addressInput.value = "Alamat tidak ditemukan, silakan ketik manual.";
                }
            } catch (error) {
                console.error("Gagal mengambil alamat:", error);
                addressInput.value = "";
            }
        }

        updateCoordinates(defaultLat, defaultLng);
        if(!addressInput.value) {
            fetchAddress(defaultLat, defaultLng);
        }

        marker.on('dragend', function (e) {
            let position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
            circle.setLatLng(position);
            fetchAddress(position.lat, position.lng);
        });

        map.on('click', function(e) {
            let lat = e.latlng.lat;
            let lng = e.latlng.lng;
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);
            updateCoordinates(lat, lng);
            fetchAddress(lat, lng);
        });

        radiusInput.addEventListener('input', function(e) {
            let newRadius = e.target.value;
            if(newRadius > 0) {
                circle.setRadius(newRadius);
            }
        });
    });
</script>
@endpush