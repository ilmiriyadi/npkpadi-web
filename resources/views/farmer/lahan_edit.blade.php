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
    
    #map img {
        max-width: none !important;
        max-height: none !important;
        width: auto !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
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
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Deskriptif (Opsional)</label>
                    <textarea name="location" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#387F39] outline-none transition-all shadow-sm bg-gray-50 focus:bg-white text-sm">{{ $land->location }}</textarea>
                </div>
            </div>

            <div class="lg:col-span-7 flex flex-col">
                <div class="mb-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Titik Koordinat Peta <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500">Geser peta dan <b>klik lokasi baru</b> jika ingin mengubah letak sawah.</p>
                </div>
                
                <div id="map" class="flex-grow shadow-inner"></div>

                <div class="flex flex-col sm:flex-row gap-3 pt-3">
                    <div class="w-full sm:w-1/2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Latitude</label>
                        <input type="text" name="latitude" id="lat" value="{{ $land->latitude }}" readonly required class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-600 font-mono outline-none cursor-not-allowed">
                    </div>
                    <div class="w-full sm:w-1/2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Longitude</label>
                        <input type="text" name="longitude" id="lng" value="{{ $land->longitude }}" readonly required class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-600 font-mono outline-none cursor-not-allowed">
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
        
        // Koordinat default (Kecamatan Belawang, Batola)
        const defaultLat = -3.1145; 
        const defaultLng = 114.6030;

        // Tentukan titik awal peta
        let startLat = (dbLat !== null) ? dbLat : defaultLat;
        let startLng = (dbLng !== null) ? dbLng : defaultLng;
        // Jika sudah ada koordinat, zoom lebih dekat (15). Jika default, zoom agak jauh (13).
        let startZoom = (dbLat !== null) ? 15 : 13;
        
        const map = L.map('map').setView([startLat, startLng], startZoom);
        let marker = null;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19
        }).addTo(map);

        // Jika data dari database ada, langsung pasang pin/marker
        if (dbLat !== null && dbLng !== null) {
            marker = L.marker([dbLat, dbLng]).addTo(map);
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
                marker = L.marker(e.latlng).addTo(map);
            }
        });
        
        setTimeout(() => {
            map.invalidateSize();
        }, 500);
    });
</script>
@endsection