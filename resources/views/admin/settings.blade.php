@extends('layouts.admin') 
@section('title', 'Pengaturan Profil')
@section('header_title', 'Pengaturan Akun')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Informasi Pribadi</h2>
        
        <!-- Pesan Sukses -->
        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 font-medium">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST"> <!-- Ganti route untuk Admin -->
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <hr class="border-gray-100 my-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Ubah Password <span class="text-sm font-normal text-gray-500">(Opsional)</span></h3>

                <!-- Password Baru -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password"
                        class="w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                        class="w-full bg-gray-50 border border-gray-200 text-gray-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class=" bg-blue-600 hover:bg-blue-800 text-white px-8 py-3 rounded-xl font-bold transition-colors shadow-sm">
                    Simpan
                </button> <!-- Ganti warna jadi bg-blue-600 untuk Admin -->
            </div>
        </form>
    </div>
@endsection