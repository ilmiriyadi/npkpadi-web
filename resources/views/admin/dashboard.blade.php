@extends('layouts.admin')

@section('title', 'Dashboard Admin - NPK Padi')
@section('header_title', 'Dashboard Utama Admin')

@section('content')
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-3xl p-8 text-white shadow-md mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 right-32 w-32 h-32 bg-white opacity-10 rounded-full -mb-10"></div>
        
        <div class="relative z-10">
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Halo, Administrator!</h2>
            <p class="text-blue-100 max-w-xl">Selamat datang di pusat kendali NPK Padi. Pantau aktivitas seluruh petani, lahan, dan riwayat deteksi AI dari sini.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mr-4">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Petani</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $totalPetani }} <span class="text-base font-normal text-gray-500">Orang</span></h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-16 h-16 rounded-2xl bg-green-50 text-green-500 flex items-center justify-center text-2xl mr-4">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Lahan</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $totalLahan }} <span class="text-base font-normal text-gray-500">Sawah</span></h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center">
            <div class="w-16 h-16 rounded-2xl bg-purple-50 text-purple-500 flex items-center justify-center text-2xl mr-4">
                <i class="fa-solid fa-microchip"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Deteksi AI</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $totalDeteksi }} <span class="text-base font-normal text-gray-500">Kali</span></h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Aktivitas Deteksi Terbaru</h3>
            <a href="{{ route('admin.history') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="bg-white text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Petani & Lahan</th>
                        <th class="px-6 py-4 font-semibold">Waktu Deteksi</th>
                        <th class="px-6 py-4 font-semibold">Hasil AI</th>
                        <th class="px-6 py-4 font-semibold text-center">Akurasi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($deteksiTerbaru as $deteksi)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-800">{{ $deteksi->land->user->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-location-dot mr-1 text-gray-400"></i>{{ $deteksi->land->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-gray-800">{{ $deteksi->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $deteksi->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">
                                @if($deteksi->nutrient_deficiency_id == 1) <span class="text-green-600">{{ $deteksi->nutrientDeficiency->name }}</span>
                                @elseif($deteksi->nutrient_deficiency_id == 2) <span class="text-yellow-600">{{ $deteksi->nutrientDeficiency->name }}</span>
                                @elseif($deteksi->nutrient_deficiency_id == 3) <span class="text-orange-600">{{ $deteksi->nutrientDeficiency->name }}</span>
                                @else <span class="text-red-600">{{ $deteksi->nutrientDeficiency->name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-full text-xs">{{ $deteksi->confidence_score }}%</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada aktivitas deteksi di sistem.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection