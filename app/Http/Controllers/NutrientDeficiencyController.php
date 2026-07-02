<?php

namespace App\Http\Controllers;

use App\Models\NutrientDeficiency;
use Illuminate\Http\Request;

class NutrientDeficiencyController extends Controller
{
    public function index()
    {
        $deficiencies = NutrientDeficiency::with('solutions')->get();
        return view('admin.datamaster', compact('deficiencies'));
    }

    public function update(Request $request, $id)
    {
        // 1. VALIDASI KETAT: Cek apakah ada umur yang bertabrakan sebelum menyimpan
        if ($this->hasOverlappingPhases($request->input('unggul_solutions'))) {
            return redirect()->back()->with('error', 'Gagal menyimpan! Ada rentang umur pada Bibit Unggul yang saling tumpang tindih. Mohon perbaiki dan coba lagi.');
        }

        if ($this->hasOverlappingPhases($request->input('lokal_solutions'))) {
            return redirect()->back()->with('error', 'Gagal menyimpan! Ada rentang umur pada Bibit Lokal yang saling tumpang tindih. Mohon perbaiki dan coba lagi.');
        }

        // Cari data berdasarkan nutrient_deficiency_id
        $deficiency = NutrientDeficiency::findOrFail($id);

        try {
            // 2. Update Saran Umum di tabel Induk
            $deficiency->update([
                'saran_umum_unggul' => $request->saran_umum_unggul,
                'saran_umum_lokal'  => $request->saran_umum_lokal,
            ]);

            // 3. Bersihkan fase HST lama
            $deficiency->solutions()->delete();

            // 4. Simpan Fase HST Bibit Unggul (Jika lolos validasi)
            if ($request->has('unggul_solutions') && is_array($request->unggul_solutions)) {
                foreach ($request->unggul_solutions as $sol) {
                    if(isset($sol['min_hst']) && isset($sol['max_hst'])) {
                        $deficiency->solutions()->create([
                            'seed_type'       => 'unggul',
                            'min_hst'         => (int) $sol['min_hst'],
                            'max_hst'         => (int) $sol['max_hst'],
                            'solution_detail' => $sol['detail'] ?? '',
                        ]);
                    }
                }
            }

            // 5. Simpan Fase HST Bibit Lokal (Jika lolos validasi)
            if ($request->has('lokal_solutions') && is_array($request->lokal_solutions)) {
                foreach ($request->lokal_solutions as $sol) {
                    if(isset($sol['min_hst']) && isset($sol['max_hst'])) {
                        $deficiency->solutions()->create([
                            'seed_type'       => 'lokal',
                            'min_hst'         => (int) $sol['min_hst'],
                            'max_hst'         => (int) $sol['max_hst'],
                            'solution_detail' => $sol['detail'] ?? '',
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Tangkap pesan error aslinya agar tampil di layar admin, bukan layar 500
            return redirect()->back()->with('error', 'SERVER ERROR: ' . $e->getMessage() . ' di baris ' . $e->getLine());
        }

        return redirect()->route('admin.datamaster')->with('success', 'Rincian saran penanganan berhasil diperbarui!');
    }

    /**
     * Fungsi Helper untuk mendeteksi apakah ada rentang umur yang bertabrakan.
     */
    private function hasOverlappingPhases($solutions)
    {
        if (empty($solutions)) return false;

        $validSolutions = [];

        // Filter data dan pastikan formatnya angka (integer)
        foreach ($solutions as $sol) {
            if (isset($sol['min_hst']) && isset($sol['max_hst'])) {
                $validSolutions[] = [
                    'min_hst' => (int) $sol['min_hst'],
                    'max_hst' => (int) $sol['max_hst']
                ];
            }
        }

        // Urutkan array berdasarkan min_hst (dari yang terkecil ke terbesar)
        usort($validSolutions, function($a, $b) {
            return $a['min_hst'] <=> $b['min_hst'];
        });

        // Loop untuk mengecek apakah umur min sekarang menabrak umur max sebelumnya
        for ($i = 1; $i < count($validSolutions); $i++) {
            if ($validSolutions[$i]['min_hst'] <= $validSolutions[$i - 1]['max_hst']) {
                return true; // Ditemukan tabrakan!
            }
        }

        return false; // Aman
    }
}