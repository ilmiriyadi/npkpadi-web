<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Land;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // ==========================================
    // AREA ADMIN
    // ==========================================

    public function admin()
    {
        // 1. Hitung total statistik keseluruhan sistem
        $totalPetani = \App\Models\User::where('role', 'farmer')->count();
        $totalLahan = \App\Models\Land::count();
        $totalDeteksi = \App\Models\Detection::count();

        // 2. Ambil 5 riwayat deteksi paling baru dari SEMUA petani
        $deteksiTerbaru = \App\Models\Detection::with(['land.user', 'nutrientDeficiency'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
        
        return view('admin.dashboard', compact('totalPetani', 'totalLahan', 'totalDeteksi', 'deteksiTerbaru'));
    }

    // Halaman Data Master (Admin)
    public function adminDataMaster()
    {
        // Ambil semua data penyakit & solusi
        $deficiencies = \App\Models\NutrientDeficiency::all();
        return view('admin.datamaster', compact('deficiencies'));
    }

    // Simpan Data Master Baru
    public function adminDataMasterStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'solution' => 'required|string',
        ]);

        \App\Models\NutrientDeficiency::create([
            'name' => $request->name,
            'solution' => $request->solution,
        ]);

        return redirect()->route('admin.datamaster')->with('success', 'Data Penyakit/Solusi baru berhasil ditambahkan!');
    }

    // Update Data Master
    public function adminDataMasterUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'solution' => 'required|string',
        ]);

        $deficiency = \App\Models\NutrientDeficiency::findOrFail($id);
        $deficiency->update([
            'name' => $request->name,
            'solution' => $request->solution,
        ]);

        return redirect()->route('admin.datamaster')->with('success', 'Data Penyakit/Solusi berhasil diperbarui!');
    }

    // Hapus Data Master
    public function adminDataMasterDestroy($id)
    {
        $deficiency = \App\Models\NutrientDeficiency::findOrFail($id);
        $deficiency->delete();

        return redirect()->route('admin.datamaster')->with('success', 'Data Penyakit/Solusi berhasil dihapus!');
    }

    // 1. Menampilkan Halaman Manajemen User
    public function adminUsers()
    {
        // Ambil semua user yang mendaftar sebagai 'farmer' (Petani)
        $farmers = \App\Models\User::where('role', 'farmer')
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Hitung statistik Lahan dan Deteksi untuk masing-masing petani
        foreach ($farmers as $farmer) {
            // UBAH $farmer->id menjadi $farmer->user_id di sini:
            $landIds = \App\Models\Land::where('user_id', $farmer->user_id)->pluck('land_id');
            
            $farmer->total_lands = count($landIds);
            $farmer->total_detections = \App\Models\Detection::whereIn('land_id', $landIds)->count();
        }

        return view('admin.users', compact('farmers'));
    }

    // 2. Memproses pembuatan akun Petani baru
    public function adminUsersStore(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.\App\Models\User::class],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'farmer', // Otomatis diset sebagai petani
        ]);

        return redirect()->route('admin.users')->with('success', 'Akun Petani baru berhasil dibuat!');
    }

    // 3. Memproses update data Petani
    public function adminUsersUpdate(Request $request, $id)
    {
        // Pastikan kita mencari berdasarkan user_id
        $user = \App\Models\User::where('user_id', $id)->firstOrFail();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // PERBAIKAN DI SINI: Kita tambahkan nama kolom 'user_id' di akhir aturan unique
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->user_id . ',user_id'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Jika form password diisi, berarti Admin mereset password Petani
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            ]);
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'Data Petani berhasil diperbarui!');
    }

    // 4. Memproses penghapusan akun Petani
    public function adminUsersDestroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Akun Petani berhasil dihapus dari sistem!');
    }

    // Halaman Riwayat Deteksi Keseluruhan (Admin)
    public function adminHistory(Request $request)
    {
        // Ambil semua deteksi beserta relasi ke tabel lahan, user (petani), dan penyakit
        $query = \App\Models\Detection::with(['land.user', 'nutrientDeficiency']);

        // Logika Pencarian (Bisa cari nama petani, lahan, atau penyakit)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('land.user', function($qUser) use ($search) {
                    $qUser->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('land', function($qLand) use ($search) {
                    $qLand->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('nutrientDeficiency', function($qNutrient) use ($search) {
                    $qNutrient->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        // Eksekusi data, urutkan dari yang paling baru
        $detections = $query->orderBy('created_at', 'desc')->get();

        return view('admin.history', compact('detections'));
    }


    // ==========================================
    // AREA PETANI
    // ==========================================

    // Halaman Dashboard Petani
    public function farmer()
    {
        $userId = Auth::id();

        // 1. Data Dasar Lahan & Deteksi
        $landIds = \App\Models\Land::where('user_id', $userId)->pluck('land_id');
        $totalLahan = $landIds->count();
        $totalDeteksi = \App\Models\Detection::whereIn('land_id', $landIds)->count();

        // 2. Data Deteksi Terbaru
        $deteksiTerbaru = \App\Models\Detection::with(['nutrientDeficiency', 'land'])
                            ->whereIn('land_id', $landIds)
                            ->orderBy('created_at', 'desc')
                            ->first();

        // 3. MENGHITUNG RINCIAN BERDASARKAN NAMA PENYAKIT (Bukan ID lagi)
        $countN = \App\Models\Detection::whereIn('land_id', $landIds)
            ->whereHas('nutrientDeficiency', function($q) {
                $q->where('name', 'like', '%Nitrogen%')->orWhere('name', 'like', '% N%');
            })->count();

        $countP = \App\Models\Detection::whereIn('land_id', $landIds)
            ->whereHas('nutrientDeficiency', function($q) {
                $q->where('name', 'like', '%Fosfor%')->orWhere('name', 'like', '% P%');
            })->count();

        $countK = \App\Models\Detection::whereIn('land_id', $landIds)
            ->whereHas('nutrientDeficiency', function($q) {
                $q->where('name', 'like', '%Kalium%')->orWhere('name', 'like', '% K%');
            })->count();

        return view('farmer.dashboard', compact(
            'totalLahan', 'totalDeteksi', 'deteksiTerbaru', 
            'countN', 'countP', 'countK'
        ));
    }

    // Halaman Kelola Lahan (Petani)
    public function farmerLahan()
    {
        // Ambil data lahan dari database yang user_id-nya sama dengan petani yang sedang login
        $lands = \App\Models\Land::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
                    ->get();

        return view('farmer.lahan', compact('lands')); 
    }

    // Memproses penyimpanan data lahan ke database
    public function farmerLahanStore(Request $request)
    {
        // Validasi inputan agar tidak boleh kosong
        $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
        ]);

        // Simpan data ke tabel 'lands'
        Land::create([
            'user_id' => Auth::id(), // Mengambil ID petani yang sedang login
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return redirect()->route('farmer.lahan')->with('success', 'Lahan baru berhasil ditambahkan!');
    }

    // Memproses perubahan data (Edit Lahan)
    public function farmerLahanUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
        ]);

        // Cari lahan berdasarkan ID dan pastikan itu milik petani yang sedang login
        $land = \App\Models\Land::where('land_id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        $land->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return redirect()->route('farmer.lahan')->with('success', 'Data lahan berhasil diperbarui!');
    }

    // Memproses penghapusan data (Hapus Lahan)
    public function farmerLahanDestroy($id)
    {
        $land = \App\Models\Land::where('land_id', $id)->where('user_id', Auth::id())->firstOrFail();
        $land->delete();

        return redirect()->route('farmer.lahan')->with('success', 'Lahan berhasil dihapus!');
    }

    // Halaman Riwayat Deteksi (Petani)
    public function farmerHistory(Request $request)
    {
        // 1. Ambil daftar Lahan milik petani ini untuk mengisi dropdown Filter
        $lands = \App\Models\Land::where('user_id', Auth::id())->get();

        // 2. Mulai susun query dasar untuk mengambil Riwayat Deteksi
        $query = \App\Models\Detection::with(['land', 'nutrientDeficiency'])
            ->whereHas('land', function($q) {
                $q->where('user_id', Auth::id());
            });

        // 3. Logika Pencarian (Search) berdasarkan nama lahan atau hasil AI
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Cari dari nama lahan
                $q->whereHas('land', function($qLand) use ($search) {
                    $qLand->where('name', 'like', '%' . $search . '%');
                })
                // Atau cari dari nama penyakit (Misal: ketik "Nitrogen")
                ->orWhereHas('nutrientDeficiency', function($qNutrient) use ($search) {
                    $qNutrient->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        // 4. Logika Filter (Dropdown Lahan)
        if ($request->filled('land_id')) {
            $query->where('land_id', $request->land_id);
        }

        // 5. Eksekusi query dan urutkan dari yang terbaru
        $detections = $query->orderBy('created_at', 'desc')->get();

        // Kirim variabel $detections dan $lands ke tampilan HTML
        return view('farmer.history', compact('detections', 'lands'));
    }
}