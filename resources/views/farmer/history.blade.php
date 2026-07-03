@extends('layouts.farmer')

@section('title', 'Riwayat Deteksi - NPK Padi')
@section('header_title', 'Riwayat Deteksi Nutrisi')

@section('content')
    <form action="{{ route('farmer.history') }}" method="GET" class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
        <div class="relative w-full md:w-96">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama lahan atau hasil deteksi..." class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm text-sm">
        </div>
        
        <div class="flex space-x-3 w-full md:w-auto">
            <select name="land_id" class="border border-gray-200 text-gray-600 text-sm rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm">
                <option value="">Semua Lahan</option>
                @foreach($lands as $land)
                    <option value="{{ $land->land_id }}" {{ request('land_id') == $land->land_id ? 'selected' : '' }}>
                        {{ $land->name }}
                    </option>
                @endforeach
            </select>
            
            <button type="submit" class="bg-[#387F39] hover:bg-green-800 text-white border border-transparent px-5 py-3 rounded-xl text-sm font-semibold transition-colors shadow-sm flex items-center justify-center tooltip" title="Terapkan Filter">
                <i class="fa-solid fa-filter md:mr-2"></i> <span class="hidden md:inline">Cari</span>
            </button>

            @if(request('search') || request('land_id'))
                <a href="{{ route('farmer.history') }}" class="bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 px-4 py-3 rounded-xl text-sm font-semibold transition-colors shadow-sm flex items-center justify-center tooltip" title="Hapus Filter">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="bg-green-50/50 text-green-800 text-sm border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap w-16 text-center">No</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap">Foto Daun</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap">Waktu Deteksi</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap">Lokasi & Bibit</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap">Umur Padi (Saat Deteksi)</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap">Hasil Analisis</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    
                    @forelse($detections as $index => $detection)
                    @php
                        // Hitung HST (Hari Setelah Tanam)
                        $rawDays = \Carbon\Carbon::parse($detection->land->planting_date)->diffInDays($detection->created_at);
                        $hst = intval($rawDays);
                        $seedType = $detection->land->seed_type ?? 'unggul';

                        // Tentukan batas maksimal panen berdasarkan bibit (Unggul: 110, Lokal: 270)
                        $batasPanen = ($seedType == 'unggul') ? 110 : 270;

                        // CEK HUMAN ERROR: Jika umur melebihi batas panen
                        if ($hst > $batasPanen) {
                            $teksSolusi = "PERINGATAN!! Umur padi di lahan ini tercatat " . $hst . " Hari Setelah Tanam (HST). Angka ini melebihi masa panen normal (" . $batasPanen . " hari untuk Bibit " . ucfirst($seedType) . ").\n\nSistem mendeteksi Anda mungkin belum memperbarui 'Tanggal Tanam' untuk musim tanam yang baru. Silakan edit Tanggal Tanam di menu 'Kelola Lahan' agar saran penanganan kembali akurat.";
                        } else {
                            // JIKA NORMAL: Cari solusi yang cocok di tabel solutions secara dinamis
                            $solusi = $detection->nutrientDeficiency->solutions()
                                ->where('seed_type', $seedType)
                                ->where('min_hst', '<=', $hst)
                                ->where('max_hst', '>=', $hst)
                                ->first();

                            // Jika ketemu saran spesifik fase, gunakan itu. Jika belum diatur admin, gunakan saran umum.
                            if ($solusi) {
                                $teksSolusi = "[Fase " . $solusi->min_hst . "-" . $solusi->max_hst . " HST] - " . $solusi->solution_detail;
                            } else {
                                $teksSolusi = "[Saran Umum] - " . (($seedType == 'unggul') ? $detection->nutrientDeficiency->saran_umum_unggul : $detection->nutrientDeficiency->saran_umum_lokal);
                            }
                        }
                        
                        // FIX: Mengamankan teks dari karakter enter (\n) dan tanda kutip agar tidak merusak tombol onClick JS
                        $teksSolusiAman = str_replace(["\r", "\n"], ["", "\\n"], addslashes($teksSolusi));
                    @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>
                            
                             <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 cursor-pointer" onclick="openDetailModal('{{ asset($detection->image_path) }}', '{{ $detection->nutrientDeficiency->name }}', '{{ round($detection->confidence_score, 2) }}', '{{ $teksSolusiAman }}', '{{ $detection->segmented_image_path ? asset($detection->segmented_image_path) : '' }}')">
                                    <img src="{{ asset($detection->image_path) }}" alt="Daun Padi" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-800">{{ $detection->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500 mt-0.5"><i class="fa-regular fa-clock mr-1"></i>{{ $detection->created_at->timezone('Asia/Makassar')->format('H:i') }} WITA</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-700">
                                    @if($detection->land->latitude && $detection->land->longitude)
                                        <a href="https://www.google.com/maps?q={{ $detection->land->latitude }},{{ $detection->land->longitude }}" target="_blank" class="text-[#387F39] hover:underline flex items-center">
                                            <i class="fa-solid fa-map-location-dot mr-2"></i> {{ $detection->land->name }}
                                        </a>
                                    @else
                                        <span class="flex items-center"><i class="fa-solid fa-location-dot text-gray-400 mr-2"></i> {{ $detection->land->name }}</span>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider 
                                        {{ $detection->land->seed_type == 'unggul' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                        {{ $detection->land->seed_type == 'unggul' ? 'Bibit Unggul' : 'Bibit Lokal' }}
                                    </span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-800">{{ $hst }} Hari</span>
                                <div class="text-xs text-gray-500 mt-0.5">Setelah Tanam (HST)</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($detection->nutrient_deficiency_id == 1) <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div> {{ $detection->nutrientDeficiency->name }}
                                    </span>
                                @elseif($detection->nutrient_deficiency_id == 2) <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                        <div class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></div> {{ $detection->nutrientDeficiency->name }}
                                    </span>
                                @elseif($detection->nutrient_deficiency_id == 3) <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                                        <div class="w-2 h-2 rounded-full bg-orange-500 mr-2"></div> {{ $detection->nutrientDeficiency->name }}
                                    </span>
                                @else <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        <div class="w-2 h-2 rounded-full bg-red-500 mr-2"></div> {{ $detection->nutrientDeficiency->name }}
                                    </span>
                                @endif
                                <div class="text-xs text-gray-500 mt-1.5 font-medium">Confidence Score: <span class="text-blue-600">{{ round($detection->confidence_score, 2) }}%</span></div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button type="button" 
                                    onclick="openDetailModal('{{ asset($detection->image_path) }}', '{{ $detection->nutrientDeficiency->name }}', '{{ round($detection->confidence_score, 2) }}', '{{ $teksSolusiAman }}', '{{ $detection->segmented_image_path ? asset($detection->segmented_image_path) : '' }}')" 
                                    class="text-[#387F39] hover:text-green-800 bg-green-50 hover:bg-green-100 px-4 py-2 rounded-xl text-sm font-semibold transition-colors tooltip" title="Lihat Saran Penanganan">
                                    Saran
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-camera-retro text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-base font-bold text-gray-700">Belum ada riwayat deteksi</p>
                                    <p class="text-sm text-gray-500 mt-1">Alat Raspberry Pi Anda belum mengirimkan data deteksi apapun ke sistem.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL -->
    <div id="detailModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">Hasil Analisis & Saran Penanganan</h3>
                        <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <div class="mt-4 flex flex-col items-center w-full">
                        <div id="modal_image_tabs" class="flex space-x-2 mb-3 bg-gray-100 p-1 rounded-xl hidden">
                            <button type="button" id="tab_btn_original" onclick="switchModalTab('original')" class="px-4 py-1.5 rounded-lg text-xs font-bold bg-white text-gray-800 shadow-sm transition-colors">Asli</button>
                            <button type="button" id="tab_btn_segmented" onclick="switchModalTab('segmented')" class="px-4 py-1.5 rounded-lg text-xs font-bold text-gray-500 hover:text-gray-800 transition-colors">Segmentasi</button>
                        </div>

                        <div class="w-full h-48 rounded-2xl overflow-hidden bg-gray-100 border border-gray-200 mb-4 relative">
                            <img id="modal_image" src="" alt="Daun Padi" class="w-full h-full object-cover">
                            <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-xs font-bold text-gray-800 shadow-sm">
                                Confidence Score: <span id="modal_accuracy" class="text-blue-600"></span>%
                            </div>
                        </div>

                        <div class="w-full text-center p-4 bg-gray-50 rounded-2xl mb-4 border border-gray-100">
                            <p class="text-sm text-gray-500 mb-1">Diagnosis Penyakit (CNN MobileNetV2):</p>
                            <h4 id="modal_disease" class="text-lg font-bold text-gray-800"></h4>
                        </div>

                        <div class="w-full p-5 bg-green-50 rounded-2xl border border-green-100">
                            <div class="flex items-center mb-2">
                                <i class="fa-solid fa-prescription-bottle-medical text-green-600 mr-2 text-lg"></i>
                                <h4 class="font-bold text-green-800">Saran Penanganan:</h4>
                            </div>
                            <!-- Tambahan whitespace-pre-wrap di sini membuat enter (\n) tampil rapi ke bawah -->
                            <p id="modal_solution" class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap"></p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="button" onclick="closeDetailModal()" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-[#387F39] text-base font-medium text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let currentOriginalPath = '';
    let currentSegmentedPath = '';

    function openDetailModal(imagePath, diseaseName, accuracy, solution, segmentedImagePath = '') {
        currentOriginalPath = imagePath;
        currentSegmentedPath = segmentedImagePath;
        
        document.getElementById('modal_image').src = imagePath;
        document.getElementById('modal_disease').innerText = diseaseName;
        document.getElementById('modal_accuracy').innerText = accuracy;
        document.getElementById('modal_solution').innerText = solution;

        const tabs = document.getElementById('modal_image_tabs');
        if (segmentedImagePath) {
            tabs.classList.remove('hidden');
            switchModalTab('segmented');
        } else {
            tabs.classList.add('hidden');
            switchModalTab('original');
        }
        
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function switchModalTab(tab) {
        const originalBtn = document.getElementById('tab_btn_original');
        const segmentedBtn = document.getElementById('tab_btn_segmented');
        const img = document.getElementById('modal_image');

        if (tab === 'original') {
            originalBtn.className = "px-4 py-1.5 rounded-lg text-xs font-bold bg-white text-gray-800 shadow-sm transition-colors";
            segmentedBtn.className = "px-4 py-1.5 rounded-lg text-xs font-bold text-gray-500 hover:text-gray-800 transition-colors";
            img.src = currentOriginalPath;
        } else {
            originalBtn.className = "px-4 py-1.5 rounded-lg text-xs font-bold text-gray-500 hover:text-gray-800 transition-colors";
            segmentedBtn.className = "px-4 py-1.5 rounded-lg text-xs font-bold bg-white text-gray-800 shadow-sm transition-colors";
            img.src = currentSegmentedPath;
        }
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }
</script>
@endsection