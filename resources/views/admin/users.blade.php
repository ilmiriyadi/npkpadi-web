@extends('layouts.admin')

@section('title', 'Manajemen Petani - NPK Padi')
@section('header_title', 'Daftar Pengguna (Petani)')

@section('content')

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl shadow-sm" role="alert">
            <div class="flex items-center mb-2">
                <i class="fa-solid fa-triangle-exclamation text-xl mr-3"></i>
                <span class="font-bold">Gagal menyimpan data! Periksa hal berikut:</span>
            </div>
            <ul class="list-disc list-inside text-sm ml-8 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Manajemen Akun Petani</h2>
            <p class="text-sm text-gray-500 mt-1">Buat akun, reset sandi, dan pantau petani yang menggunakan alat Anda.</p>
        </div>
        
        <button onclick="openAddModal()" class="w-full md:w-auto bg-blue-600 hover:bg-blue-800 text-white px-6 py-3 rounded-xl text-sm font-semibold transition-colors shadow-sm flex items-center justify-center transform hover:-translate-y-0.5">
            <i class="fa-solid fa-user-plus mr-2"></i> Tambah Petani Baru
        </button>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="bg-blue-50/50 text-blue-800 text-sm border-b border-gray-100 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap w-16 text-center">No</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap">Profil Petani</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap">Tanggal Bergabung</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap text-center">Statistik</th>
                        <th class="px-6 py-5 font-semibold whitespace-nowrap text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($farmers as $index => $farmer)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-lg mr-3 border border-green-200">
                                        {{ substr($farmer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $farmer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $farmer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-gray-800 font-medium">{{ $farmer->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $farmer->created_at->diffForHumans() }}</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-2">
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-600 tooltip" title="Total Lahan">
                                        <i class="fa-solid fa-map-location-dot mr-1.5"></i> {{ $farmer->total_lands }}
                                    </span>
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-md text-xs font-bold bg-green-50 text-green-600 tooltip" title="Total Deteksi">
                                        <i class="fa-solid fa-camera mr-1.5"></i> {{ $farmer->total_detections }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <!-- Tombol Reset Password (Kuning) -->
                                    <button onclick="openResetModal('{{ $farmer->user_id }}', '{{ addslashes($farmer->name) }}')" class="w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 flex items-center justify-center transition-colors tooltip" title="Reset Password">
                                        <i class="fa-solid fa-key text-xs"></i>
                                    </button>
                                    
                                    <!-- Tombol Hapus (Merah) -->
                                    <form action="{{ route('admin.users.destroy', $farmer->user_id) }}" method="POST" onsubmit="return confirm('YAKIN HAPUS PETANI INI? Semua data lahan dan riwayat deteksinya akan terhapus.');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-colors tooltip" title="Hapus Akun">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-users-slash text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-base font-bold text-gray-700">Belum ada petani yang terdaftar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH (TETAP SAMA) -->
    <div id="addModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeAddModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-6 sm:p-8 sm:pb-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-xl leading-6 font-bold text-gray-900">Buat Akun Petani Baru</h3>
                            <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Petani <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required placeholder="Contoh: Budi Santoso" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" required placeholder="Contoh: budi@gmail.com" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm text-sm">
                            </div>
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mt-2">
                                <p class="text-xs text-blue-600">
                                    <i class="fa-solid fa-circle-info mr-1"></i> 
                                    Sandi akun akan otomatis disetel ke: <strong>petani123</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-blue-600 text-base font-medium text-white hover:bg-blue-800 transition-colors sm:ml-3 sm:w-auto sm:text-sm">Buat Akun</button>
                        <button type="button" onclick="closeAddModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL RESET PASSWORD (PENGGANTI EDIT MODAL) -->
    <div id="resetModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeResetModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
                <form id="resetForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-6 sm:p-8 sm:pb-6">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xl leading-6 font-bold text-gray-900">Konfirmasi Reset Sandi</h3>
                            <button type="button" onclick="closeResetModal()" class="text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        <p class="text-sm text-gray-500 mb-5">Anda akan mereset kata sandi untuk akun: <strong id="reset_farmer_name" class="text-gray-800 text-base"></strong></p>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-center shadow-inner">
                            <p class="text-sm font-medium text-yellow-800 mb-2">Kata sandi akan dikembalikan ke default sistem:</p>
                            <div class="inline-block bg-white border-2 border-yellow-300 font-mono text-xl font-bold px-6 py-2 rounded-lg text-yellow-700 tracking-widest shadow-sm">
                                petani123
                            </div>
                            <p class="text-xs text-yellow-600 mt-3"><i class="fa-solid fa-circle-info mr-1"></i> Petani dapat mengubahnya nanti di menu Pengaturan.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-yellow-500 text-base font-bold text-white hover:bg-yellow-600 transition-colors sm:ml-3 sm:w-auto sm:text-sm flex items-center">
                            <i class="fa-solid fa-rotate mr-2"></i> Ya, Reset Sekarang
                        </button>
                        <button type="button" onclick="closeResetModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Modal Tambah Petani
    function openAddModal() { document.getElementById('addModal').classList.remove('hidden'); }
    function closeAddModal() { document.getElementById('addModal').classList.add('hidden'); }

    // Modal Reset Password (pengganti Modal Edit)
    function openResetModal(id, name) {
        document.getElementById('resetForm').action = '/admin/users/' + id;
        document.getElementById('reset_farmer_name').innerText = name;
        document.getElementById('resetModal').classList.remove('hidden');
    }
    function closeResetModal() { document.getElementById('resetModal').classList.add('hidden'); }
</script>
@endsection