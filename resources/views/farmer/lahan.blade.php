@extends('layouts.farmer')

@section('title', 'Kelola Lahan - NPK Padi')
@section('header_title', 'Kelola Lahan Saya')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 space-y-4 md:space-y-0">
        <a href="{{ route('farmer.lahan.create') }}" class="w-full md:w-auto bg-[#387F39] hover:bg-green-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-colors shadow-sm flex items-center justify-center transform hover:-translate-y-0.5">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Lahan Baru
        </a>
    </div>
    
    @if($lands->isEmpty())
        <div class="col-span-full text-center py-16 bg-white rounded-3xl border border-gray-100 shadow-sm">
            <div class="w-24 h-24 mx-auto bg-green-50 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-seedling text-4xl text-green-500"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">Belum ada lahan yang terdaftar</h3>
            <p class="text-gray-500 mt-2 mb-6">Silakan tambah lahan baru untuk mulai memantau nutrisi padi Anda.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($lands as $land)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all duration-300 flex flex-col h-full">
                <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-bl-full z-0 transition-transform group-hover:scale-110 duration-300"></div>
                
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $land->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fa-solid fa-calendar-plus mr-1"></i> Ditambahkan: {{ $land->created_at->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('farmer.lahan.edit', $land->land_id) }}" class="text-gray-400 hover:text-blue-500 transition-colors tooltip" title="Edit Lahan">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        <form action="{{ route('farmer.lahan.destroy', $land->land_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lahan ini? Semua riwayat deteksi di lahan ini akan ikut terhapus permanen.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors tooltip" title="Hapus Lahan">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-3 text-sm text-gray-600 mb-6 relative z-10">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fa-solid fa-location-dot text-gray-400"></i>
                        </div>
                        
                        <div class="mt-1 flex-1 min-w-0 break-words pr-2">
                            <span class="font-medium text-gray-800">Lokasi:</span> <br> 
                            {{ $land->location ?? 'Belum ada detail lokasi' }}
                            
                            {{-- Tampilkan Koordinat Jika Ada --}}
                            @if($land->latitude && $land->longitude)
                                <a href="https://www.google.com/maps?q={{ $land->latitude }},{{ $land->longitude }}" target="_blank" class="mt-2 inline-flex items-center text-xs font-semibold text-[#387F39] bg-green-50 hover:bg-green-100 border border-green-200 px-3 py-1.5 rounded-lg transition-colors w-fit group">
                                    <i class="fa-solid fa-map-location-dot mr-2 text-green-600 group-hover:scale-110 transition-transform"></i>
                                    <span>
                                        Buka Peta ({{ number_format($land->latitude, 4) }}, {{ number_format($land->longitude, 4) }})
                                    </span>
                                    <i class="fa-solid fa-arrow-up-right-from-square ml-2 text-[10px] opacity-70"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-3 text-sm text-gray-600 mb-6 relative z-10">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fa-solid fa-calendar-days text-gray-400"></i>
                        </div>
                        <p class="mt-1"><span class="font-medium text-gray-800">Tanggal Tanam:</span> <br> {{ $land->planting_date ? \Carbon\Carbon::parse($land->planting_date)->format('d M Y') : 'Belum ada detail tanggal tanam'}}</p>
                    </div>
                </div>

                <div class="space-y-3 text-sm text-gray-600 mb-6 relative z-10">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fa-solid fa-seedling text-gray-400"></i>
                        </div>
                        <p class="mt-1"><span class="font-medium text-gray-800">Jenis Bibit:</span> <br> {{ $land->seed_type == 'unggul' ? 'Bibit Unggul' : 'Bibit Lokal' }}</p>
                    </div>
                </div>

                <a href="{{ route('farmer.history', ['land_id' => $land->land_id]) }}" class="bg-green-100 text-green-700 hover:bg-green-200 px-4 py-2 rounded-xl text-sm font-semibold transition-colors w-full block text-center mt-auto relative z-10">
                    Lihat Data Deteksi <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
            @endforeach
        </div>
    @endif
@endsection

@section('scripts')
<script>
    // Script untuk Modal Tambah (Sudah ada)
    function openModal() { document.getElementById('lahanModal').classList.remove('hidden'); }
    function closeModal() { document.getElementById('lahanModal').classList.add('hidden'); }

    // TAMBAHKAN Script untuk Modal Edit ini:
    function openEditModal(id, name, location, planting_date, seed_type) {
        // 1. Ubah alamat tujuan form sesuai ID lahan yang diklik
        document.getElementById('editLahanForm').action = '/farmer/lahan/' + id;
        
        // 2. Isi otomatis inputan dengan data lama
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_location').value = location;
        document.getElementById('edit_planting_date').value = planting_date;
        document.getElementById('edit_seed_type').value = seed_type;
        
        // 3. Tampilkan modalnya
        document.getElementById('editLahanModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editLahanModal').classList.add('hidden');
    }
</script>
@endsection