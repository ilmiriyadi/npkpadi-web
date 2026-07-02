@extends('layouts.admin')

@section('title', 'Data Master Saran - NPK Padi')
@section('header_title', 'Kelola Data Master Penyakit')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Penyakit & Saran Penanganan</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola saran penanganan berdasarkan jenis bibit yang akan dibaca oleh petani.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg shadow-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg shadow-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-r-xl">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-circle-info text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">Info: "Nama Kondisi" telah dikunci agar selalu sinkron dengan label kelas AI (MobileNetV2). Anda hanya dapat memperbarui rincian saran penanganan.</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-sm">
                        <th class="py-4 px-6 font-semibold w-16">ID</th>
                        <th class="py-4 px-6 font-semibold">Nama Kondisi</th>
                        <th class="py-4 px-6 font-semibold text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($deficiencies as $index => $deficiency)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6 text-gray-600">{{ $index + 1 }}</td>
                        <td class="py-4 px-6 font-bold text-red-500">{{ $deficiency->name }}</td>
                        <td class="py-4 px-6 text-center">
                            <button onclick="openModal('modal-{{ $deficiency->nutrient_deficiency_id }}')" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition font-semibold text-sm flex items-center justify-center w-full">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit Data
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @foreach($deficiencies as $deficiency)
    <div id="modal-{{ $deficiency->nutrient_deficiency_id }}" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 transition-opacity" onclick="closeModal('modal-{{ $deficiency->nutrient_deficiency_id }}')"></div>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle max-w-4xl w-full">
                <form action="{{ route('admin.datamaster.update', $deficiency->nutrient_deficiency_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="px-8 py-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Edit Saran Penanganan</h3>
                            <button type="button" onclick="closeModal('modal-{{ $deficiency->nutrient_deficiency_id }}')" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Kondisi / Penyakit</label>
                            <input type="text" value="{{ $deficiency->name }}" class="w-full border-gray-200 bg-gray-50 text-gray-500 rounded-lg text-sm cursor-not-allowed" disabled>
                        </div>

                        <div class="border-b border-gray-200 mb-6">
                            <nav class="-mb-px flex space-x-8">
                                <button type="button" onclick="switchTab('unggul', {{ $deficiency->nutrient_deficiency_id }})" id="tab-btn-unggul-{{ $deficiency->nutrient_deficiency_id }}" class="tab-btn-{{ $deficiency->nutrient_deficiency_id }} border-blue-500 text-blue-600 whitespace-nowrap py-2 px-1 border-b-2 font-semibold text-sm transition">
                                    Saran Bibit Unggul
                                </button>
                                <button type="button" onclick="switchTab('lokal', {{ $deficiency->nutrient_deficiency_id }})" id="tab-btn-lokal-{{ $deficiency->nutrient_deficiency_id }}" class="tab-btn-{{ $deficiency->nutrient_deficiency_id }} border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-semibold text-sm transition">
                                    Saran Bibit Lokal
                                </button>
                            </nav>
                        </div>

                        <div id="tab-content-unggul-{{ $deficiency->nutrient_deficiency_id }}" class="tab-content-{{ $deficiency->nutrient_deficiency_id }}">
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Saran Umum (Wajib Diisi) <span class="text-red-500">*</span></label>
                                <textarea name="saran_umum_unggul" rows="2" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" required placeholder="Saran penanganan jika petani tidak mengisi umur HST...">{{ $deficiency->saran_umum_unggul }}</textarea>
                            </div>

                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-sm font-semibold text-gray-700">Saran Spesifik Berdasarkan Fase Umur (HST)</label>
                                <button type="button" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-bold transition" onclick="addPhaseRow('unggul', {{ $deficiency->nutrient_deficiency_id }})">
                                    + Tambah Fase Umur
                                </button>
                            </div>
                            
                            <div id="container-unggul-{{ $deficiency->nutrient_deficiency_id }}" class="space-y-3">
                                @foreach($deficiency->solutions->where('seed_type', 'unggul') as $sol)
                                <div class="relative bg-gray-50 border border-gray-200 p-4 rounded-lg mt-3">
                                    <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-xs font-bold bg-red-50 px-2 py-1 rounded">X Hapus</button>
                                    <div class="flex space-x-4 mb-2 w-3/4">
                                        <div class="w-1/2">
                                            <label class="block text-xs text-gray-500 mb-1">Umur Min (HST)</label>
                                            <input type="number" name="unggul_solutions[existing_{{ $sol->id }}][min_hst]" value="{{ $sol->min_hst }}" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500" required>
                                        </div>
                                        <div class="w-1/2">
                                            <label class="block text-xs text-gray-500 mb-1">Umur Max (HST)</label>
                                            <input type="number" name="unggul_solutions[existing_{{ $sol->id }}][max_hst]" value="{{ $sol->max_hst }}" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500" required>
                                        </div>
                                    </div>
                                    <textarea name="unggul_solutions[existing_{{ $sol->id }}][detail]" rows="2" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500" required>{{ $sol->solution_detail }}</textarea>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div id="tab-content-lokal-{{ $deficiency->nutrient_deficiency_id }}" class="tab-content-{{ $deficiency->nutrient_deficiency_id }} hidden">
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Saran Umum (Wajib Diisi) <span class="text-red-500">*</span></label>
                                <textarea name="saran_umum_lokal" rows="2" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" required placeholder="Saran penanganan jika petani tidak mengisi umur HST...">{{ $deficiency->saran_umum_lokal }}</textarea>
                            </div>

                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-sm font-semibold text-gray-700">Saran Spesifik Berdasarkan Fase Umur (HST)</label>
                                <button type="button" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-bold transition" onclick="addPhaseRow('lokal', {{ $deficiency->nutrient_deficiency_id }})">
                                    + Tambah Fase Umur
                                </button>
                            </div>
                            
                            <div id="container-lokal-{{ $deficiency->nutrient_deficiency_id }}" class="space-y-3">
                                @foreach($deficiency->solutions->where('seed_type', 'lokal') as $sol)
                                <div class="relative bg-gray-50 border border-gray-200 p-4 rounded-lg mt-3">
                                    <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-xs font-bold bg-red-50 px-2 py-1 rounded">X Hapus</button>
                                    <div class="flex space-x-4 mb-2 w-3/4">
                                        <div class="w-1/2">
                                            <label class="block text-xs text-gray-500 mb-1">Umur Min (HST)</label>
                                            <input type="number" name="lokal_solutions[existing_{{ $sol->id }}][min_hst]" value="{{ $sol->min_hst }}" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500" required>
                                        </div>
                                        <div class="w-1/2">
                                            <label class="block text-xs text-gray-500 mb-1">Umur Max (HST)</label>
                                            <input type="number" name="lokal_solutions[existing_{{ $sol->id }}][max_hst]" value="{{ $sol->max_hst }}" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500" required>
                                        </div>
                                    </div>
                                    <textarea name="lokal_solutions[existing_{{ $sol->id }}][detail]" rows="2" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500" required>{{ $sol->solution_detail }}</textarea>
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                    
                    <div class="bg-gray-50 px-8 py-4 rounded-b-2xl flex justify-end space-x-3 border-t">
                        <button type="button" onclick="closeModal('modal-{{ $deficiency->nutrient_deficiency_id }}')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-2 px-6 rounded-lg transition">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition shadow-md">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

@endsection

@section('scripts')
<script>
    function openModal(modalID) {
        document.getElementById(modalID).classList.remove('hidden');
    }
    
    function closeModal(modalID) {
        document.getElementById(modalID).classList.add('hidden');
    }

    function switchTab(type, id) {
        let contents = document.querySelectorAll('.tab-content-' + id);
        contents.forEach(el => el.classList.add('hidden'));

        let buttons = document.querySelectorAll('.tab-btn-' + id);
        buttons.forEach(el => {
            el.classList.remove('border-blue-500', 'text-blue-600');
            el.classList.add('border-transparent', 'text-gray-500');
        });

        document.getElementById('tab-content-' + type + '-' + id).classList.remove('hidden');
        let activeBtn = document.getElementById('tab-btn-' + type + '-' + id);
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('border-blue-500', 'text-blue-600');
    }

    function addPhaseRow(type, id) {
        let container = document.getElementById('container-' + type + '-' + id);
        let uniqueId = 'new_' + Date.now();
        
        let html = `
        <div class="relative bg-gray-50 border border-gray-200 p-4 rounded-lg mt-3">
            <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-xs font-bold bg-red-50 px-2 py-1 rounded transition">X Hapus</button>
            <div class="flex space-x-4 mb-2 w-3/4">
                <div class="w-1/2">
                    <label class="block text-xs text-gray-500 mb-1">Umur Min (HST)</label>
                    <input type="number" name="${type}_solutions[${uniqueId}][min_hst]" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500" placeholder="0" required>
                </div>
                <div class="w-1/2">
                    <label class="block text-xs text-gray-500 mb-1">Umur Max (HST)</label>
                    <input type="number" name="${type}_solutions[${uniqueId}][max_hst]" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500" placeholder="40" required>
                </div>
            </div>
            <textarea name="${type}_solutions[${uniqueId}][detail]" rows="2" class="w-full border-gray-300 rounded text-sm focus:ring-blue-500" placeholder="Ketik saran..." required></textarea>
        </div>`;
        
        container.insertAdjacentHTML('beforeend', html);
    }
</script>
@endsection