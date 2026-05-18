@extends('layouts.farmer')

@section('title', 'Dashboard Petani - NPK Padi')
@section('header_title', 'Dashboard Utama')

@section('content')
    <div class="bg-gradient-to-r from-[#387F39] to-green-600 rounded-3xl p-8 text-white shadow-md mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 right-32 w-32 h-32 bg-white opacity-10 rounded-full -mb-10"></div>
        
        <div class="relative z-10">
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Halo, {{ Auth::user()->name }}!</h2>
            <p class="text-green-50 max-w-xl">Selamat datang di panel pemantauan nutrisi padi. Pantau kesehatan tanaman dan sebaran nutrisi di lahan Anda di sini.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mr-6">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Lahan Terdaftar</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $totalLahan }} <span class="text-base font-normal text-gray-500">Sawah</span></h3>
            </div>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-16 h-16 rounded-2xl bg-green-50 text-green-500 flex items-center justify-center text-2xl mr-6">
                <i class="fa-solid fa-microchip"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Deteksi AI</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $totalDeteksi }} <span class="text-base font-normal text-gray-500">Kali</span></h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 text-center">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kurang Nitrogen (N)</p>
            <div class="text-2xl font-black text-yellow-500">{{ $countN }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 text-center">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kurang Fosfor (P)</p>
            <div class="text-2xl font-black text-orange-500">{{ $countP }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 text-center">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kurang Kalium (K)</p>
            <div class="text-2xl font-black text-red-500">{{ $countK }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 lg:col-span-1">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Grafik Sebaran Kondisi</h3>
            <div class="relative h-64 w-full flex items-center justify-center">
                @if($totalDeteksi > 0)
                    <canvas id="kondisiChart"></canvas>
                @else
                    <p class="text-gray-400 text-sm text-center">Grafik akan muncul setelah ada data deteksi.</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Status Pemantauan Terakhir</h3>
            
            @if($deteksiTerbaru)
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                    <div class="w-full md:w-40 h-40 rounded-2xl overflow-hidden bg-gray-100 flex-shrink-0">
                        <img src="{{ asset($deteksiTerbaru->image_path) }}" alt="Daun Padi" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="flex-1 w-full">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-500"><i class="fa-regular fa-clock mr-1"></i> {{ $deteksiTerbaru->created_at->diffForHumans() }}</span>
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">{{ $deteksiTerbaru->land->name }}</span>
                        </div>
                        
                        <h4 class="text-xl font-bold text-gray-800 mb-2">
                            @if($deteksiTerbaru->nutrient_deficiency_id == 1) <span class="text-green-600">{{ $deteksiTerbaru->nutrientDeficiency->name }}</span>
                            @elseif($deteksiTerbaru->nutrient_deficiency_id == 2) <span class="text-yellow-600">{{ $deteksiTerbaru->nutrientDeficiency->name }}</span>
                            @elseif($deteksiTerbaru->nutrient_deficiency_id == 3) <span class="text-orange-600">{{ $deteksiTerbaru->nutrientDeficiency->name }}</span>
                            @else <span class="text-red-600">{{ $deteksiTerbaru->nutrientDeficiency->name }}</span>
                            @endif
                        </h4>
                        
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed line-clamp-2">
                            <span class="font-semibold">Saran:</span> {{ $deteksiTerbaru->nutrientDeficiency->solution }}
                        </p>
                        
                        <a href="{{ route('farmer.history') }}" class="inline-flex items-center px-4 py-2 bg-green-50 text-green-700 hover:bg-green-100 rounded-xl font-semibold text-sm transition-colors">
                            Lihat Selengkapnya <i class="fa-solid fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-3 text-2xl text-gray-300">
                        <i class="fa-solid fa-camera-retro"></i>
                    </div>
                    <p class="text-gray-500 text-sm">Belum ada foto yang dikirim dari perangkat IoT.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Hanya render grafik jika ada data
    @if($totalDeteksi > 0)
        const ctx = document.getElementById('kondisiChart').getContext('2d');
        const kondisiChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Kurang N', 'Kurang P', 'Kurang K'], // Hapus 'Sehat'
                datasets: [{
                    data: [{{ $countN }}, {{ $countP }}, {{ $countK }}], // Hapus $countSehat
                    backgroundColor: [
                        '#eab308', // Kuning (Kurang N)
                        '#f97316', // Orange (Kurang P)
                        '#ef4444'  // Merah (Kurang K)
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: "'Inter', sans-serif", size: 12 }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    @endif
</script>
@endsection