<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Land;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Throwable;

class DashboardController extends Controller
{
    private const PDF_HISTORY_LIMIT = 50;

    // ==========================================
    // AREA ADMIN
    // ==========================================

    public function admin(Request $request)
    {
        // Hitung total statistik keseluruhan sistem
        $totalPetani = \App\Models\User::where('role', 'farmer')->count();
        $totalLahan = \App\Models\Land::count();
        $totalDeteksi = \App\Models\Detection::count();

        // Ambil 5 riwayat deteksi paling baru dari SEMUA petani
        $deteksiTerbaru = \App\Models\Detection::with(['land.user', 'nutrientDeficiency'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        // ==========================================
        // GRAFIK NPK (DENGAN CUSTOM DATE RANGE)
        // ==========================================
        
        // Tangkap inputan tanggal dari Admin. Jika kosong, default ke 7 hari terakhir
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Buat rentang tanggal (Array of Dates) menggunakan CarbonPeriod
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        
        $dates = [];
        $nitrogen = [];
        $fosfor = [];
        $kalium = [];

        // Agregasi kueri grafik NPK dalam satu kueri tunggal untuk menghindari N+1 query
        $detectionsGrouped = \App\Models\Detection::selectRaw('DATE(created_at) as date, nutrient_deficiency_id, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date', 'nutrient_deficiency_id')
            ->get()
            ->groupBy('date');

        // Looping setiap tanggal yang ada di dalam rentang waktu tersebut
        foreach ($period as $dateObj) {
            $dateString = $dateObj->format('Y-m-d');
            $dates[] = $dateString; // Simpan tanggal aslinya

            // Ambil data deteksi harian dari koleksi hasil grouping
            $dayDetections = $detectionsGrouped->get($dateString, collect());

            $nItem = $dayDetections->where('nutrient_deficiency_id', 1)->first();
            $pItem = $dayDetections->where('nutrient_deficiency_id', 2)->first();
            $kItem = $dayDetections->where('nutrient_deficiency_id', 3)->first();

            $nitrogen[] = $nItem ? $nItem->count : 0;
            $fosfor[]   = $pItem ? $pItem->count : 0;
            $kalium[]   = $kItem ? $kItem->count : 0;
        }

        $chartLabels = collect($dates)->map(function($d) {
            return \Carbon\Carbon::parse($d)->locale('id')->translatedFormat('D, d M');
        })->toArray();

        // Passing variabel tanggal agar form di web tetap menampilkan tanggal yang dipilih
        return view('admin.dashboard', compact(
            'totalPetani', 'totalLahan', 'totalDeteksi', 'deteksiTerbaru',
            'chartLabels', 'nitrogen', 'fosfor', 'kalium', 
            'startDate', 'endDate'
        ));
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
            'solution_vegetative' => 'nullable|string',
            'solution_generative' => 'nullable|string',
            'solution_ripening' => 'nullable|string',
        ]);

        \App\Models\NutrientDeficiency::create([
            'name' => $request->name,
            'solution' => $request->solution,
            'solution_vegetative' => $request->solution_vegetative,
            'solution_generative' => $request->solution_generative,
            'solution_ripening' => $request->solution_ripening,
        ]);

        return redirect()->route('admin.datamaster')->with('success', 'Data Penyakit/Solusi baru berhasil ditambahkan!');
    }

    // Update Data Master
    public function adminDataMasterUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'solution' => 'required|string',
            'solution_vegetative' => 'nullable|string',
            'solution_generative' => 'nullable|string',
            'solution_ripening' => 'nullable|string',
        ]);

        $deficiency = \App\Models\NutrientDeficiency::findOrFail($id);
        $deficiency->update([
            'name' => $request->name,
            'solution' => $request->solution,
            'solution_vegetative' => $request->solution_vegetative,
            'solution_generative' => $request->solution_generative,
            'solution_ripening' => $request->solution_ripening,
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
                    ->withCount(['lands', 'detections'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Hitung statistik Lahan dan Deteksi untuk masing-masing petani (menggunakan data count relasi)
        foreach ($farmers as $farmer) {
            $farmer->total_lands = $farmer->lands_count;
            $farmer->total_detections = $farmer->detections_count;
        }

        return view('admin.users', compact('farmers'));
    }

    // 2. Memproses pembuatan akun Petani baru
    public function adminUsersStore(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.\App\Models\User::class],
        ]);

        $defaultPassword = 'petani123';

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($defaultPassword),
            'role' => 'farmer', // Otomatis diset sebagai petani
        ]);

        return redirect()->route('admin.users')->with('success', 'Akun Petani baru berhasil ditambahkan dengan sandi bawaan: ' . $defaultPassword);
    }

    // 3. Memproses RESET PASSWORD Petani
    public function adminUsersUpdate(Request $request, $id)
    {
        $user = \App\Models\User::where('user_id', $id)->firstOrFail();

        // 1. Tentukan password default-nya di sini
        $defaultPassword = 'petani123'; 

        // 2. Langsung update tanpa perlu validasi request (karena tidak ada form input)
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($defaultPassword),
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'Sandi akun ' . $user->name . ' berhasil direset ke default: ' . $defaultPassword);
    }

    // 4. Memproses penghapusan akun Petani
    public function adminUsersDestroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Akun Petani berhasil dihapus dari sistem!');
    }

    // 5. pengaturan profil admin
    public function adminSettings()
    {
        $user = Auth::user();
        return view('admin.settings', compact('user'));
    }

    public function adminSettingsUpdate(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'password' => 'nullable|confirmed|min:8', // Opsional, hanya jika diisi
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $user->save();

        return redirect()->back()->with('success', 'Profil Admin berhasil diperbarui!');
    }

    // Halaman Riwayat Deteksi Keseluruhan (Admin)
    public function adminHistory(Request $request)
    {
        $query = \App\Models\Detection::with(['land.user', 'nutrientDeficiency']);

        // 1. Filter Pencarian Teks (Petani & Lahan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('land', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // 2. Filter Jenis Bibit
        if ($request->filled('seed_type')) {
            $query->whereHas('land', function($q) use ($request) {
                $q->where('seed_type', $request->seed_type);
            });
        }

        // 3. Filter Jenis Defisiensi
        if ($request->filled('deficiency')) {
            $query->where('nutrient_deficiency_id', $request->deficiency);
        }

        // 4. Filter Rentang Waktu (Date Range)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $perPage = $request->input('per_page', 10);

        $detections = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->query());

        $lands = \App\Models\Land::all();

        return view('admin.history', compact('detections', 'lands'));
    }

    // PDF Halaman Riwayat Admin
    public function adminHistoryPdf(Request $request)
    {
        $query = \App\Models\Detection::with(['land.user', 'nutrientDeficiency.solutions']);

        // 1. Filter Pencarian Teks (Petani & Lahan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('land', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // 2. Filter Jenis Bibit
        if ($request->filled('seed_type')) {
            $query->whereHas('land', function($q) use ($request) {
                $q->where('seed_type', $request->seed_type);
            });
        }

        // 3. Filter Jenis Defisiensi
        if ($request->filled('deficiency')) {
            $query->where('nutrient_deficiency_id', $request->deficiency);
        }

        // 4. Filter Rentang Waktu (Date Range)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $totalDetections = (clone $query)->count();
        $detections = $query->latest()->limit(self::PDF_HISTORY_LIMIT)->get();

        return $this->downloadHistoryPdf($detections, 'Laporan_Aktivitas_Admin.pdf', $totalDetections);
    }


    // ==========================================
    // AREA PETANI
    // ==========================================

    // Halaman Pengaturan Profil Petani
    public function farmerSettings()
    {
        $user = Auth::user();
        return view('farmer.settings', compact('user'));
    }

    public function farmerSettingsUpdate(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->user_id.',user_id',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $user->save();

        return redirect()->back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

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
            'planting_date' => 'required|date',
            'seed_type' => 'required|in:unggul,lokal',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Simpan data ke tabel 'lands'
        Land::create([
            'user_id' => Auth::id(), // Mengambil ID petani yang sedang login
            'name' => $request->name,
            'location' => $request->location,
            'planting_date' => $request->planting_date,
            'seed_type' => $request->seed_type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('farmer.lahan')->with('success', 'Lahan baru berhasil ditambahkan!');
    }

    // Menampilkan halaman form tambah lahan
    public function farmerLahanCreate()
    {
        return view('farmer.lahan_create');
    }

    // Menampilkan halaman form edit lahan
    public function farmerLahanEdit($id)
    {
        $land = \App\Models\Land::where('user_id', \Illuminate\Support\Facades\Auth::id())->findOrFail($id);
        return view('farmer.lahan_edit', compact('land'));
    }

    // Memproses perubahan data (Edit Lahan)
    public function farmerLahanUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'planting_date' => 'required|date',
            'seed_type' => 'required|in:unggul,lokal',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Cari lahan berdasarkan ID dan pastikan itu milik petani yang sedang login
        $land = \App\Models\Land::where('land_id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        $land->update([
            'name' => $request->name,
            'location' => $request->location,
            'planting_date' => $request->planting_date,
            'seed_type' => $request->seed_type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
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
        $lands = \App\Models\Land::where('user_id', Auth::id())->get();

        $query = \App\Models\Detection::with(['land', 'nutrientDeficiency'])
            ->whereHas('land', function($q) {
                $q->where('user_id', auth()->id());
            });

        // 1. Filter Pencarian Teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('land', function($qLand) use ($search) {
                    $qLand->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        // 2. Filter Lahan Spesifik
        if ($request->filled('land_id')) {
            $query->where('land_id', $request->land_id);
        }

        // 3. Filter Jenis Bibit
        if ($request->filled('seed_type')) {
            $query->whereHas('land', function($q) use ($request) {
                $q->where('seed_type', $request->seed_type);
            });
        }

        // 4. Filter Jenis Defisiensi (Hasil Klasifikasi)
        if ($request->filled('deficiency')) {
            $query->where('nutrient_deficiency_id', $request->deficiency);
        }

        // 5. Filter Rentang Waktu (Date Range)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $perPage = $request->input('per_page', 10);

        $detections = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->query());

        return view('farmer.history', compact('detections', 'lands'));
    }

    // PDF Halaman Riwayat Petani
    public function farmerHistoryPdf(Request $request)
    {
        $query = \App\Models\Detection::with(['land.user', 'nutrientDeficiency.solutions'])
            ->whereHas('land', function($q) {
                $q->where('user_id', auth()->id());
            });

        // 1. Filter Pencarian Teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('land', function($qLand) use ($search) {
                    $qLand->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        // 2. Filter Lahan Spesifik
        if ($request->filled('land_id')) {
            $query->where('land_id', $request->land_id);
        }

        // 3. Filter Jenis Bibit
        if ($request->filled('seed_type')) {
            $query->whereHas('land', function($q) use ($request) {
                $q->where('seed_type', $request->seed_type);
            });
        }

        // 4. Filter Jenis Defisiensi
        if ($request->filled('deficiency')) {
            $query->where('nutrient_deficiency_id', $request->deficiency);
        }

        // 5. Filter Rentang Waktu (Date Range)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $totalDetections = (clone $query)->count();
        $detections = $query->latest()->limit(self::PDF_HISTORY_LIMIT)->get();

        return $this->downloadHistoryPdf($detections, 'Riwayat_Deteksi_Petani.pdf', $totalDetections);
    }

    private function downloadHistoryPdf($detections, string $filename, int $totalDetections)
    {
        $pdfLimit = self::PDF_HISTORY_LIMIT;

        try {
            return Pdf::loadView('pdf.history', compact('detections', 'totalDetections', 'pdfLimit'))
                ->setPaper('A4', 'landscape')
                ->download($filename);
        } catch (Throwable $e) {
            Log::error('Gagal membuat PDF riwayat deteksi.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()
                ->view('pdf.history', compact('detections', 'totalDetections', 'pdfLimit'))
                ->header('Content-Type', 'text/html; charset=UTF-8');
        }
    }
}
