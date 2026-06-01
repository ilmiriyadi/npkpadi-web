<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SyncController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 1. Jalur ini berfungsi sebagai "Satpam" yang mengecek Role
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect('/admin/dashboard');
    }
    return redirect('/farmer/dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ==========================================
// RUTE KHUSUS ADMIN
// ==========================================
// Kita bungkus semua jalur Admin agar otomatis punya awalan /admin dan bernama admin.
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    
    // Ruangan Khusus Admin
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Halaman Data Master Admin
    Route::get('/datamaster', [DashboardController::class, 'adminDataMaster'])->name('datamaster');
            
    // Rute CRUD Data Master
    Route::post('/datamaster', [DashboardController::class, 'adminDataMasterStore'])->name('datamaster.store');
    Route::put('/datamaster/{id}', [DashboardController::class, 'adminDataMasterUpdate'])->name('datamaster.update');
    Route::delete('/datamaster/{id}', [DashboardController::class, 'adminDataMasterDestroy'])->name('datamaster.destroy');

    // Halaman Riwayat Admin
    Route::get('/riwayat', [DashboardController::class, 'adminHistory'])->name('history');

    // Halaman Pengaturan Profil Admin
    Route::get('/settings', [DashboardController::class, 'adminSettings'])->name('settings');
    Route::put('/settings', [DashboardController::class, 'adminSettingsUpdate'])->name('settings.update');

    // Halaman Manajemen User
    Route::get('/users', [DashboardController::class, 'adminUsers'])->name('users');    
    Route::post('/users', [DashboardController::class, 'adminUsersStore'])->name('users.store');
    Route::put('/users/{id}', [DashboardController::class, 'adminUsersUpdate'])->name('users.update');
    Route::delete('/users/{id}', [DashboardController::class, 'adminUsersDestroy'])->name('users.destroy');    

});


// ==========================================
// RUTE KHUSUS PETANI
// ==========================================
Route::prefix('farmer')->name('farmer.')->middleware('auth')->group(function () {
    
    // Halaman Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'farmer'])->name('dashboard');
        
    // Halaman Kelola Lahan
    Route::get('/lahan', [DashboardController::class, 'farmerLahan'])->name('lahan');
        
    // Rute untuk Proses Simpan Lahan
    Route::post('/lahan', [DashboardController::class, 'farmerLahanStore'])->name('lahan.store');
    
     // Rute untuk Edit dan Hapus Lahan
    Route::put('/lahan/{id}', [DashboardController::class, 'farmerLahanUpdate'])->name('lahan.update');
    Route::delete('/lahan/{id}', [DashboardController::class, 'farmerLahanDestroy'])->name('lahan.destroy');

    // Halaman Tambah dan Edit Lahan
    Route::get('/lahan/create', [DashboardController::class, 'farmerLahanCreate'])->name('lahan.create');
    Route::get('/lahan/{id}/edit', [DashboardController::class, 'farmerLahanEdit'])->name('lahan.edit');

    // Halaman Riwayat Deteksi
    Route::get('/riwayat', [DashboardController::class, 'farmerHistory'])->name('history');

    // Halaman Pengaturan Profil Petani
    Route::get('/settings', [DashboardController::class, 'farmerSettings'])->name('settings');
    Route::put('/settings', [DashboardController::class, 'farmerSettingsUpdate'])->name('settings.update');

});


// ==========================================
// RUTE BAWAAN LARAVEL BREEZE (PROFILE & AUTH)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// RUTE API SYNC DARI RASPBERRY PI
// ==========================================
// Endpoint untuk komunikasi machine-to-machine (Pi → VPS).
// Menggunakan Bearer token auth, bukan session/CSRF.
Route::prefix('api/sync')->middleware('api.auth')->group(function () {

    // Terima batch hasil deteksi dari Pi
    Route::post('/detections', [SyncController::class, 'receiveBatchDetections'])->name('sync.detections');

    // Kirim daftar lahan ke Pi (untuk offline cache)
    Route::get('/lands', [SyncController::class, 'getLands'])->name('sync.lands');

    // Kirim daftar petani ke Pi (untuk login screen)
    Route::get('/farmers', [SyncController::class, 'getFarmers'])->name('sync.farmers');

});

require __DIR__.'/auth.php';