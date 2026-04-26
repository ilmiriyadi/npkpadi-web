@extends('layouts.admin')

@section('title', 'Data Master Solusi - NPK Padi')
@section('header_title', 'Kelola Data Master Penyakit')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Penyakit & Solusi</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola rekomendasi penanganan yang akan dibaca oleh petani.</p>
        </div>
    </div>

    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-xl">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700 font-medium">
                    Peringatan: Jika Anda mengubah "Nama Penyakit", pastikan namanya tetap relevan dengan label/kelas yang dihasilkan oleh model AI (CNN ResNet18) agar sistem tetap sinkron.
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
                        <th class="px-6 py-5 font-semibold">Teks Solusi / Penanganan</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($deficiencies as $data)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-center font-bold text-gray-500">{{ $data->nutrient_deficiency_id }}</td>
                            
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                @if(str_contains(strtolower($data->name), 'sehat'))
                                    <span class="text-green-600"><i class="fa-solid fa-circle-check mr-1"></i> {{ $data->name }}</span>
                                @else
                                    <span class="text-red-500">{{ $data->name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 leading-relaxed">{{ $data->solution }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="openEditModal('{{ $data->nutrient_deficiency_id }}', '{{ addslashes($data->name) }}', '{{ addslashes($data->solution) }}')" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors tooltip" title="Edit Data">
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
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-6 sm:p-8 sm:pb-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-xl leading-6 font-bold text-gray-900">Edit Data Master</h3>
                            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kondisi / Penyakit <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="edit_name" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Solusi Penanganan <span class="text-red-500">*</span></label>
                                <textarea name="solution" id="edit_solution" rows="4" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-sm"></textarea>
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

    function openEditModal(id, name, solution) {
        document.getElementById('editForm').action = '/admin/datamaster/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_solution').value = solution;
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
</script>
@endsection