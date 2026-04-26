@extends('layouts.admin')

@section('title', 'Pantau Riwayat AI - NPK Padi')
@section('header_title', 'Pantau Seluruh Riwayat Deteksi')

@section('content')
    <form action="{{ route('admin.history') }}" method="GET" class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Semua Aktivitas AI</h2>
            <p class="text-sm text-gray-500 mt-1">Pantau foto dan hasil deteksi AI dari seluruh lahan petani.</p>
        </div>
        
        <div class="flex space-x-3 w-full md:w-auto">
            <div class="relative w-full md:w-72">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama petani, lahan..." class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-sm">
            </div>
            
            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white px-5 py-3 rounded-xl text-sm font-semibold transition-colors shadow-sm flex items-center justify-center">
                <i class="fa-solid fa-filter md:mr-2"></i> <span class="hidden md:inline">Cari</span>
            </button>

            @if(request('search'))
                <a href="{{ route('admin.history') }}" class="bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 px-4 py-3 rounded-xl text-sm font-semibold transition-colors flex items-center justify-center tooltip" title="Reset Pencarian">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="bg-blue-50/50 text-blue-800 text-sm border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap w-16 text-center">No</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap">Foto Daun</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap">Pemilik Lahan</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap">Waktu Deteksi</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap">Hasil Analisis AI</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($detections as $index => $detection)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 cursor-pointer" onclick="openDetailModal('{{ $detection->image_path }}', '{{ $detection->nutrientDeficiency->name }}', '{{ $detection->confidence_score }}', '{{ addslashes($detection->nutrientDeficiency->solution) }}')">
                                    <img src="{{ $detection->image_path }}" alt="Daun Padi" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-800">{{ $detection->land->user->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-location-dot mr-1 text-gray-400"></i>{{ $detection->land->name }}</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-gray-800 font-medium">{{ $detection->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500 mt-0.5"><i class="fa-regular fa-clock mr-1"></i>{{ $detection->created_at->format('H:i') }} WIB</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $name = strtolower($detection->nutrientDeficiency->name);
                                    $badgeClass = "bg-red-100 text-red-700";
                                    $dotClass = "bg-red-500";
                                    
                                    if(str_contains($name, 'nitrogen')) { $badgeClass = "bg-green-100 text-green-700"; $dotClass = "bg-green-500"; }
                                    elseif(str_contains($name, 'fosfor')) { $badgeClass = "bg-orange-100 text-orange-700"; $dotClass = "bg-orange-500"; }
                                    elseif(str_contains($name, 'kalium')) { $badgeClass = "bg-yellow-100 text-yellow-700"; $dotClass = "bg-yellow-500"; }
                                @endphp

                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badgeClass }}">
                                    <div class="w-2 h-2 rounded-full {{ $dotClass }} mr-2"></div> {{ $detection->nutrientDeficiency->name }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1.5 font-medium">Akurasi AI: <span class="text-blue-600">{{ $detection->confidence_score }}%</span></div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button type="button" 
                                    onclick="openDetailModal('{{ $detection->image_path }}', '{{ $detection->nutrientDeficiency->name }}', '{{ $detection->confidence_score }}', '{{ addslashes($detection->nutrientDeficiency->solution) }}')" 
                                    class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
                                    Detail Solusi
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-camera-retro text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-base font-bold text-gray-700">Belum ada riwayat deteksi</p>
                                    <p class="text-sm text-gray-500 mt-1">Belum ada data deteksi yang masuk ke sistem.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="detailModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeDetailModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl leading-6 font-bold text-gray-900">Hasil Analisis & Solusi</h3>
                        <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <div class="mt-4 flex flex-col items-center">
                        <div class="w-full h-48 rounded-2xl overflow-hidden bg-gray-100 border border-gray-200 mb-4 relative">
                            <img id="modal_image" src="" alt="Daun Padi" class="w-full h-full object-cover">
                            <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-xs font-bold text-gray-800 shadow-sm">
                                Akurasi AI: <span id="modal_accuracy" class="text-blue-600"></span>%
                            </div>
                        </div>

                        <div class="w-full text-center p-4 bg-gray-50 rounded-2xl mb-4 border border-gray-100">
                            <p class="text-sm text-gray-500 mb-1">Diagnosis Penyakit (CNN ResNet18):</p>
                            <h4 id="modal_disease" class="text-lg font-bold text-gray-800"></h4>
                        </div>

                        <div class="w-full p-5 bg-blue-50 rounded-2xl border border-blue-100">
                            <div class="flex items-center mb-2">
                                <i class="fa-solid fa-prescription-bottle-medical text-blue-600 mr-2 text-lg"></i>
                                <h4 class="font-bold text-blue-800">Saran Penanganan:</h4>
                            </div>
                            <p id="modal_solution" class="text-sm text-gray-700 leading-relaxed"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="button" onclick="closeDetailModal()" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-blue-600 text-base font-medium text-white hover:bg-blue-800 transition-colors sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openDetailModal(imagePath, diseaseName, accuracy, solution) {
        document.getElementById('modal_image').src = imagePath;
        document.getElementById('modal_disease').innerText = diseaseName;
        document.getElementById('modal_accuracy').innerText = accuracy;
        document.getElementById('modal_solution').innerText = solution;
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }
</script>
@endsection