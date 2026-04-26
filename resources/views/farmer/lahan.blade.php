@extends('layouts.farmer')

@section('title', 'Kelola Lahan - NPK Padi')
@section('header_title', 'Kelola Lahan Saya')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 space-y-4 md:space-y-0">
        <div class="relative w-full md:w-96">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-gray-400"></i>
            <input type="text" placeholder="Cari nama lahan sawah..." class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm text-sm">
        </div>
        
        <button onclick="openModal()" class="w-full md:w-auto bg-[#387F39] hover:bg-green-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-colors shadow-sm flex items-center justify-center transform hover:-translate-y-0.5">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Lahan Baru
        </button>
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
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-bl-full z-0 transition-transform group-hover:scale-110 duration-300"></div>
                
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $land->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fa-solid fa-calendar-plus mr-1"></i> Ditambahkan: {{ $land->created_at->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" onclick="openEditModal('{{ $land->land_id }}', '{{ addslashes($land->name) }}', '{{ addslashes($land->location) }}')" class="text-gray-400 hover:text-blue-500 transition-colors tooltip" title="Edit Lahan">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        <form action="{{ route('farmer.lahan.destroy', $land->land_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lahan ini? Semua riwayat deteksi AI di lahan ini akan ikut terhapus permanen.');">
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
                        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center mr-3 flex-shrink-0"><i class="fa-solid fa-location-dot text-gray-400"></i></div>
                        <p class="mt-1"><span class="font-medium text-gray-800">Lokasi:</span> <br> {{ $land->location ?? 'Belum ada detail lokasi' }}</p>
                    </div>
                </div>

                <a href="{{ route('farmer.history') }}" class="block w-full text-center bg-green-50 hover:bg-[#C8E6C9] text-green-800 font-semibold py-3 rounded-xl transition-colors text-sm relative z-10">
                    Lihat Data Nutrisi AI
                </a>
            </div>
            @endforeach
        </div>
    @endif

    <!-- Modal Tambah Lahan Baru -->
    <div id="lahanModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form action="{{ route('farmer.lahan.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-6 sm:p-8 sm:pb-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">Tambah Lahan Baru</h3>
                            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lahan <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" required placeholder="Contoh: Sawah Blok Selatan" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm text-sm">
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Detail Lokasi (Opsional)</label>
                                <textarea name="location" id="location" rows="3" placeholder="Contoh: Desa Suka Maju, RT 01/RW 02" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-[#387F39] text-base font-medium text-white hover:bg-green-800 transition-colors sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Lahan
                        </button>
                        <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Lahan -->
    <div id="editLahanModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeEditModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                
                <form id="editLahanForm" method="POST">
                    @csrf
                    @method('PUT') <div class="bg-white px-6 pt-6 pb-6 sm:p-8 sm:pb-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-xl leading-6 font-bold text-gray-900">Edit Data Lahan</h3>
                            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lahan <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="edit_name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-sm">
                            </div>
                            <div>
                                <label for="edit_location" class="block text-sm font-medium text-gray-700 mb-1">Detail Lokasi</label>
                                <textarea name="location" id="edit_location" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 transition-colors sm:ml-3 sm:w-auto sm:text-sm">Simpan Perubahan</button>
                        <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Script untuk Modal Tambah (Sudah ada)
    function openModal() { document.getElementById('lahanModal').classList.remove('hidden'); }
    function closeModal() { document.getElementById('lahanModal').classList.add('hidden'); }

    // TAMBAHKAN Script untuk Modal Edit ini:
    function openEditModal(id, name, location) {
        // 1. Ubah alamat tujuan form sesuai ID lahan yang diklik
        document.getElementById('editLahanForm').action = '/farmer/lahan/' + id;
        
        // 2. Isi otomatis inputan dengan data lama
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_location').value = location;
        
        // 3. Tampilkan modalnya
        document.getElementById('editLahanModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editLahanModal').classList.add('hidden');
    }
</script>
@endsection