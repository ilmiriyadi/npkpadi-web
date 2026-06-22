@extends('layouts.admin')

@section('title', 'Data Master Saran - NPK Padi')
@section('header_title', 'Kelola Data Master Penyakit')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Penyakit & Saran Penanganan</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola rekomendasi penanganan 3 fase umur yang akan dibaca oleh petani.</p>
        </div>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-r-xl">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-circle-info text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700 font-medium">
                    Info: "Nama Kondisi" telah dikunci agar selalu sinkron dengan label/kelas yang dihasilkan oleh model AI (CNN MobileNetV2). Anda hanya dapat memperbarui bagian rincian rekomendasi/saran penanganan.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 text-sm border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap w-16 text-center">ID</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap w-48">Nama Kondisi</th>
                        <th class="px-6 py-5 font-semibold">Rincian Saran (Berdasarkan Umur / HST)</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($deficiencies as $data)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-center font-bold text-gray-500">{{ $data->nutrient_deficiency_id }}</td>
                            
                            <td class="px-6 py-4 font-semibold text-gray-800 align-top pt-5">
                                @if(str_contains(strtolower($data->name), 'sehat'))
                                    <span class="text-green-600"><i class="fa-solid fa-circle-check mr-1"></i> {{ $data->name }}</span>
                                @else
                                    <span class="text-red-500">{{ $data->name }}</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-gray-600">
                                <div class="space-y-3">
                                    <div>
                                        <span class="inline-block px-2 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-md mb-1">Saran Umum</span>
                                        <p class="text-sm leading-relaxed">{{ $data->solution }}</p>
                                    </div>
                                    @if($data->solution_vegetative)
                                    <div>
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-md mb-1">Fase Vegetatif (0-40 HST)</span>
                                        <p class="text-sm leading-relaxed">{{ $data->solution_vegetative }}</p>
                                    </div>
                                    @endif
                                    @if($data->solution_generative)
                                    <div>
                                        <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-md mb-1">Fase Generatif (41-60 HST)</span>
                                        <p class="text-sm leading-relaxed">{{ $data->solution_generative }}</p>
                                    </div>
                                    @endif
                                    @if($data->solution_ripening)
                                    <div>
                                        <span class="inline-block px-2 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-md mb-1">Fase Pemasakan (>60 HST)</span>
                                        <p class="text-sm leading-relaxed">{{ $data->solution_ripening }}</p>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-center align-top pt-5">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="openEditModal(
                                        '{{ $data->nutrient_deficiency_id }}', 
                                        '{{ addslashes($data->name) }}', 
                                        '{{ addslashes($data->solution) }}',
                                        '{{ addslashes($data->solution_vegetative) }}',
                                        '{{ addslashes($data->solution_generative) }}',
                                        '{{ addslashes($data->solution_ripening) }}'
                                    )" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors tooltip" title="Edit Data">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada data master. Silakan tambah data baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeEditModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-6 sm:p-8 sm:pb-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-xl leading-6 font-bold text-gray-900">Edit Data Master & Saran Penanganan</h3>
                            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kondisi / Penyakit <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="edit_name" required readonly 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed focus:outline-none shadow-sm text-sm" 
                                title="Nama penyakit dikunci agar sinkron dengan AI">
                            <p class="text-xs text-gray-400 mt-1"><i class="fa-solid fa-lock mr-1"></i> Nama tidak dapat diubah untuk menjaga sinkronisasi dengan AI.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Saran Umum <span class="text-red-500">*</span></label>
                                <textarea name="solution" id="edit_solution" rows="4" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-sm" placeholder="Saran umum jika umur HST tidak diketahui..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fase Vegetatif (0-40 HST)</label>
                                <textarea name="solution_vegetative" id="edit_vegetative" rows="4" class="w-full px-4 py-3 rounded-xl border border-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm text-sm" placeholder="Saran Penanganan untuk umur 0-40 hari..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fase Generatif (41-60 HST)</label>
                                <textarea name="solution_generative" id="edit_generative" rows="4" class="w-full px-4 py-3 rounded-xl border border-yellow-200 focus:outline-none focus:ring-2 focus:ring-yellow-500 shadow-sm text-sm" placeholder="Saran Penanganan untuk umur 41-60 hari..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fase Pemasakan (>60 HST)</label>
                                <textarea name="solution_ripening" id="edit_ripening" rows="4" class="w-full px-4 py-3 rounded-xl border border-orange-200 focus:outline-none focus:ring-2 focus:ring-orange-500 shadow-sm text-sm" placeholder="Saran Penanganan untuk umur di atas 60 hari..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-blue-600 text-base font-medium text-white hover:bg-blue-800 transition-colors sm:ml-3 sm:w-auto sm:text-sm">Simpan Perubahan</button>
                        <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openAddModal() { document.getElementById('addModal').classList.remove('hidden'); }
    function closeAddModal() { document.getElementById('addModal').classList.add('hidden'); }

    // MENERIMA 6 PARAMETER DATA SEKARANG
    function openEditModal(id, name, solution, veg, gen, rip) {
        document.getElementById('editForm').action = '/admin/datamaster/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_solution').value = solution;
        document.getElementById('edit_vegetative').value = veg;
        document.getElementById('edit_generative').value = gen;
        document.getElementById('edit_ripening').value = rip;
        document.getElementById('editModal').classList.remove('hidden');
    }
    
    function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
</script>
@endsection