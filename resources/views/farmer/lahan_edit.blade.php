@extends('layouts.farmer')

@section('title', 'Edit Lahan - NPK Padi')
@section('header_title', 'Edit Data Lahan')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Tinggi peta dirampingkan agar proporsional */
    #map {
        height: 280px !important; 
        width: 100% !important;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        z-index: 10;
        position: relative;
    }
    
    @media (min-width: 1024px) {
        #map { height: 350px !important; }
    }
    
    .leaflet-container img {
        max-width: none !important;
        max-height: none !important;
    }
</style>

<div class="max-w-5xl mx-auto bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100">
    
    <div class="mb-6 pb-4 border-b border-gray-100">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Edit Lahan Sawah</h2>
        <p class="text-gray-500 text-sm mt-1">Ubah informasi atau geser titik koordinat jika ada perubahan.</p>
    </div>

    <form action="{{ route('farmer.lahan.update', $land->land_id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 mb-6">
            
            <div class="lg:col-span-5 space-y-4 md:space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lahan <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ $land->name }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#387F39] outline-none transition-all shadow-sm bg-gray-50 focus:bg-white text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Tanam <span class="text-red-500">*</span></label>
                    <input type="date" name="planting_date" value="{{ $land->planting_date ? \Carbon\Carbon::parse($land->planting_date)->format('Y-m-d') : '' }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#387F39] outline-none transition-all shadow-sm bg-gray-50 focus:bg-white text-sm">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Jenis Bibit Padi <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex items-center justify-center py-3 px-4 border rounded-xl cursor-pointer hover:bg-green-50 transition-all border-gray-200">
                            <input type="radio" name="seed_type" value="unggul" class="hidden peer" required 
                                {{ $land->seed_type == 'unggul' ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-gray-600 peer-checked:text-[#387F39] peer-checked:font-bold">
                                Bibit Unggul
                            </span>
                        </label>

                        <label class="relative flex items-center justify-center py-3 px-4 border rounded-xl cursor-pointer hover:bg-green-50 transition-all border-gray-200">
                            <input type="radio" name="seed_type" value="lokal" class="hidden peer" required 
                                {{ $land->seed_type == 'lokal' ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-gray-600 peer-checked:text-[#387F39] peer-checked:font-bold">
                                Bibit Lokal
                            </span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Deskriptif (Opsional)</label>
                    <textarea name="location" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#387F39] outline-none transition-all shadow-sm bg-gray-50 focus:bg-white text-sm">{{ $land->location }}</textarea>
                </div>
            </div>

            <div class="lg:col-span-7 flex flex-col">
                <div class="mb-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Titik Koordinat Peta (Opsional)</label>
                        <p class="text-xs text-gray-500 leading-relaxed">Geser peta atau klik tombol GPS.</p>
                    </div>
                    <button type="button" id="btn-gps" class="w-full sm:w-auto bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 px-3 py-2 rounded-lg text-xs font-bold transition-colors flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-location-crosshairs mr-1.5"></i> Gunakan GPS HP
                    </button>
                </div>
                
                <div id="map" class="flex-grow shadow-inner"></div>

                <div class="flex flex-col sm:flex-row gap-3 pt-3">
                    <div class="w-full sm:w-1/2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Latitude</label>
                        <input type="text" name="latitude" id="lat" value="{{ $land->latitude }}" readonly placeholder="Belum diatur" class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-600 font-mono outline-none cursor-not-allowed">
                    </div>
                    <div class="w-full sm:w-1/2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Longitude</label>
                        <input type="text" name="longitude" id="lng" value="{{ $land->longitude }}" readonly placeholder="Belum diatur" class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-600 font-mono outline-none cursor-not-allowed">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end border-t border-gray-100 pt-5 mt-2 gap-3">
            
            <a href="{{ route('farmer.lahan') }}" class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all flex items-center justify-center text-center">
                Batal
            </a>
            
            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-800 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm hover:shadow-md flex items-center justify-center">
                <i class="fa-solid fa-save mr-2"></i> Simpan
            </button>
            
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil data koordinat lama dari database (jika ada)
        const dbLat = {{ $land->latitude ?? 'null' }};
        const dbLng = {{ $land->longitude ?? 'null' }};

        const defaultLat = -3.1465548676673087; 
        const defaultLng = 114.63686286411333;

        // Tentukan titik awal peta
        let startLat = (dbLat !== null) ? dbLat : defaultLat;
        let startLng = (dbLng !== null) ? dbLng : defaultLng;
        // Jika sudah ada koordinat, zoom lebih dekat (15). Jika default, zoom agak jauh (13).
        let startZoom = (dbLat !== null) ? 15 : 13;
        
        const map = L.map('map').setView([startLat, startLng], startZoom);
        let marker = null;

        // Menggunakan Peta Satelit (Google Hybrid)
        L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '© Google Maps'
        }).addTo(map);

        // Pin Marker Warna Hijau
        const greenIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Jika data dari database ada, langsung pasang pin/marker
        if (dbLat !== null && dbLng !== null) {
            marker = L.marker([dbLat, dbLng], {icon: greenIcon}).addTo(map);
        }

        // Event listener jika user mengklik peta untuk mengubah lokasi
        map.on('click', function(e) {
            const clickedLat = e.latlng.lat.toFixed(8);
            const clickedLng = e.latlng.lng.toFixed(8);

            document.getElementById('lat').value = clickedLat;
            document.getElementById('lng').value = clickedLng;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, {icon: greenIcon}).addTo(map);
            }
        });
        
        // ==========================================
        // FITUR AMBIL LOKASI GPS (GEOLOCATION API)
        // ==========================================
        document.getElementById('btn-gps').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            
            // Ubah tombol jadi loading
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Mencari lokasi...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude.toFixed(8);
                    const lng = position.coords.longitude.toFixed(8);
                    
                    document.getElementById('lat').value = lat;
                    document.getElementById('lng').value = lng;
                    
                    const newLatLng = new L.LatLng(lat, lng);
                    map.setView(newLatLng, 18); // Zoom dekat ke lokasi
                    
                    if (marker) {
                        marker.setLatLng(newLatLng);
                    } else {
                        marker = L.marker(newLatLng, {icon: greenIcon}).addTo(map);
                    }

                    // Kembalikan tombol sukses
                    btn.innerHTML = '<i class="fa-solid fa-check mr-1.5"></i> Lokasi Ditemukan';
                    btn.classList.replace('text-blue-600', 'text-green-600');
                    btn.classList.replace('bg-blue-50', 'bg-green-50');
                    btn.classList.replace('border-blue-200', 'border-green-200');
                    
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.classList.replace('text-green-600', 'text-blue-600');
                        btn.classList.replace('bg-green-50', 'bg-blue-50');
                        btn.classList.replace('border-green-200', 'border-blue-200');
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    }, 3000);

                }, function(error) {
                    alert("Gagal mengambil lokasi GPS. Pastikan izin akses lokasi (Location/GPS) diaktifkan di pengaturan HP atau browser Anda.");
                    btn.innerHTML = originalText;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }, { enableHighAccuracy: true });
            } else {
                alert("Browser atau perangkat Anda tidak mendukung fitur lokasi GPS.");
                btn.innerHTML = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });

        setTimeout(() => {
            map.invalidateSize();
        }, 500);
    });
</script>
@endsection