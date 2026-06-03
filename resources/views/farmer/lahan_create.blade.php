@extends('layouts.farmer')

@section('title', 'Tambah Lahan - NPK Padi')
@section('header_title', 'Tambah Lahan Baru')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Tinggi peta disesuaikan agar proporsional dan tidak kebesaran */
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
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Detail Lahan Sawah</h2>
        <p class="text-gray-500 text-sm mt-1">Lengkapi informasi di bawah ini.</p>
    </div>

    <form action="{{ route('farmer.lahan.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 mb-6">
            
            <div class="lg:col-span-5 space-y-4 md:space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lahan <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Sawah Blok Selatan" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#387F39] outline-none transition-all shadow-sm bg-gray-50 focus:bg-white text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Tanam <span class="text-red-500">*</span></label>
                    <input type="date" name="planting_date" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#387F39] outline-none transition-all shadow-sm bg-gray-50 focus:bg-white text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Deskriptif (Opsional)</label>
                    <textarea name="location" rows="3" placeholder="Contoh: Desa Suka Maju, RT 01/RW 02" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#387F39] outline-none transition-all shadow-sm bg-gray-50 focus:bg-white text-sm"></textarea>
                </div>
            </div>

            <div class="lg:col-span-7 flex flex-col">
                <div class="mb-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Titik Koordinat Peta (Opsional)</label>
                    <p class="text-xs text-gray-500 leading-relaxed">Geser peta dan klik lokasi persis sawah Anda. <br><span class="text-[#387F39] font-medium bg-green-50 px-1 py-0.5 rounded">Biarkan kosong jika Anda kesulitan menemukan titik lokasinya.</span></p>
                </div>
                
                <div id="map" class="flex-grow shadow-inner"></div>

                <div class="flex flex-col sm:flex-row gap-3 pt-3">
                    <div class="w-full sm:w-1/2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Latitude</label>
                        <input type="text" name="latitude" id="lat" readonly placeholder="Belum diatur" class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-600 font-mono outline-none cursor-not-allowed">
                    </div>
                    <div class="w-full sm:w-1/2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Longitude</label>
                        <input type="text" name="longitude" id="lng" readonly placeholder="Belum diatur" class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-600 font-mono outline-none cursor-not-allowed">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end border-t border-gray-100 pt-5 mt-2 gap-3">
            
            <a href="{{ route('farmer.lahan') }}" class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all flex items-center justify-center text-center">
                Batal
            </a>
            
            <button type="submit" class="w-full sm:w-auto bg-[#387F39] hover:bg-green-800 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm hover:shadow-md flex items-center justify-center">
                <i class="fa-solid fa-save mr-2"></i> Simpan Data
            </button>
            
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const defaultLat = -3.1145; 
        const defaultLng = 114.6030;
        
        const map = L.map('map').setView([defaultLat, defaultLng], 13);
        let marker = null;

        // UPGRADE 1: Menggunakan Peta Satelit (Google Hybrid)
        L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '© Google Maps'
        }).addTo(map);

        // UPGRADE 2: Membuat Pin Marker Warna Hijau
        const greenIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        map.on('click', function(e) {
            const clickedLat = e.latlng.lat.toFixed(8);
            const clickedLng = e.latlng.lng.toFixed(8);

            document.getElementById('lat').value = clickedLat;
            document.getElementById('lng').value = clickedLng;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                // Memasang Pin Hijau saat di-klik
                marker = L.marker(e.latlng, {icon: greenIcon}).addTo(map);
            }
        });
        
        setTimeout(() => {
            map.invalidateSize();
        }, 500);
    });
</script>
@endsection