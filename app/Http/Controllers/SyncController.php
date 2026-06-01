<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Detection;
use App\Models\Land;
use App\Models\NutrientDeficiency;
use App\Models\User;

/**
 * =====================================================================
 * SyncController — API untuk menerima data dari Raspberry Pi
 * =====================================================================
 * 
 * Controller ini menangani komunikasi machine-to-machine antara
 * Raspberry Pi (Flask) dan Laravel VPS.
 * 
 * Endpoints:
 *   POST /api/sync/detections  → Terima batch hasil deteksi dari Pi
 *   GET  /api/sync/lands       → Kirim daftar lahan ke Pi
 *   GET  /api/sync/farmers     → Kirim daftar petani ke Pi
 * 
 * Auth: Bearer token (SYNC_API_TOKEN di .env)
 * =====================================================================
 */
class SyncController extends Controller
{
    /**
     * Mapping nama label AI → nama di tabel nutrient_deficiencies.
     * Sesuaikan dengan data master yang sudah ada di database.
     */
    private $labelMapping = [
        'Kalium (K)'  => ['Kalium', 'K', 'Kalium (K)'],
        'Nitrogen (N)' => ['Nitrogen', 'N', 'Nitrogen (N)'],
        'Fosfor (P)'   => ['Fosfor', 'P', 'Fosfor (P)'],
    ];

    /**
     * POST /api/sync/detections
     * 
     * Menerima batch hasil deteksi dari Raspberry Pi dan menyimpannya
     * ke database Laravel.
     * 
     * Request JSON:
     * {
     *   "user_id": 1,
     *   "detections": [
     *     {
     *       "local_id": 1,
     *       "land_id": 2,
     *       "land_name": "Sawah Utara",
     *       "label": "Nitrogen (N)",
     *       "confidence": 95.32,
     *       "all_probabilities": "{...}",
     *       "image_base64": "...",
     *       "created_at": "2026-04-24 10:30:00"
     *     },
     *     ...
     *   ]
     * }
     */
    public function receiveBatchDetections(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|integer',
            'detections' => 'required|array|min:1',
            'detections.*.label'      => 'required|string',
            'detections.*.confidence' => 'required|numeric',
        ]);

        $userId     = $request->input('user_id');
        $detections = $request->input('detections');
        $synced     = [];
        $errors     = [];

        foreach ($detections as $index => $det) {
            try {
                // 1. Cari nutrient_deficiency_id berdasarkan label
                $nutrientDeficiencyId = $this->findNutrientDeficiencyId($det['label']);
                
                if (!$nutrientDeficiencyId) {
                    $errors[] = [
                        'local_id' => $det['local_id'] ?? $index,
                        'error'    => "Label '{$det['label']}' tidak ditemukan di data master",
                    ];
                    continue;
                }

                // 2. Tentukan land_id
                $landId = $this->resolveLandId($det, $userId);

                // 3. Simpan gambar jika ada
                $imagePath = null;
                if (!empty($det['image_base64'])) {
                    $imagePath = $this->saveImage($det['image_base64'], $det['local_id'] ?? $index);
                }

                $segmentedImagePath = null;
                if (!empty($det['segmented_image_base64'])) {
                    $segmentedImagePath = $this->saveImage($det['segmented_image_base64'], ($det['local_id'] ?? $index) . '_segmented');
                }

                // 4. Buat record deteksi
                $detection = Detection::create([
                    'land_id'                => $landId,
                    'nutrient_deficiency_id' => $nutrientDeficiencyId,
                    'image_path'             => $imagePath ?? 'no-image.jpg',
                    'segmented_image_path'   => $segmentedImagePath,
                    'confidence_score'       => $det['confidence'], // Simpan sebagai 0-100
                    'is_synced'              => true,
                ]);

                // Override created_at jika dikirim dari Pi
                if (!empty($det['created_at'])) {
                    $detection->created_at = $det['created_at'];
                    $detection->save();
                }

                $synced[] = [
                    'local_id'     => $det['local_id'] ?? $index,
                    'detection_id' => $detection->detection_id,
                ];

            } catch (\Exception $e) {
                $errors[] = [
                    'local_id' => $det['local_id'] ?? $index,
                    'error'    => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success'      => true,
            'synced_count' => count($synced),
            'synced'       => $synced,
            'errors'       => $errors,
            'message'      => count($synced) . ' deteksi berhasil disinkronkan' . 
                             (count($errors) > 0 ? ', ' . count($errors) . ' gagal' : ''),
        ]);
    }

    /**
     * GET /api/sync/lands
     * 
     * Mengirim daftar lahan milik user tertentu ke Raspberry Pi.
     * Pi menyimpan data ini secara lokal agar bisa dipakai saat offline.
     * 
     * Query params:
     *   ?user_id=1
     */
    public function getLands(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
        ]);

        $userId = $request->input('user_id');
        $lands  = Land::where('user_id', $userId)
                      ->select('land_id', 'name', 'location')
                      ->orderBy('name')
                      ->get();

        return response()->json([
            'success' => true,
            'user_id' => $userId,
            'lands'   => $lands,
        ]);
    }

    /**
     * GET /api/sync/farmers
     * 
     * Mengirim daftar user petani ke Raspberry Pi.
     * Pi menampilkan list ini untuk dipilih saat startup.
     * Petani hanya bisa ditambahkan via admin panel di website.
     */
    public function getFarmers()
    {
        $farmers = User::where('role', 'farmer')
                       ->select('user_id', 'name', 'email')
                       ->orderBy('name')
                       ->get();

        return response()->json([
            'success' => true,
            'farmers' => $farmers,
        ]);
    }

    // =================================================================
    // PRIVATE HELPERS
    // =================================================================

    /**
     * Cari nutrient_deficiency_id dari label AI.
     * Cocokkan dengan fleksibel (bisa "Nitrogen (N)" atau "Nitrogen" atau "N").
     */
    private function findNutrientDeficiencyId(string $label): ?int
    {
        // Coba exact match dulu
        $deficiency = NutrientDeficiency::where('name', $label)->first();
        if ($deficiency) {
            return $deficiency->nutrient_deficiency_id;
        }

        // Coba fuzzy match berdasarkan mapping
        foreach ($this->labelMapping as $aiLabel => $possibleNames) {
            if ($label === $aiLabel || in_array($label, $possibleNames)) {
                foreach ($possibleNames as $name) {
                    $deficiency = NutrientDeficiency::where('name', 'LIKE', '%' . $name . '%')->first();
                    if ($deficiency) {
                        return $deficiency->nutrient_deficiency_id;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Tentukan land_id yang valid.
     * Jika land_id dari Pi tidak valid (misal 0 atau tidak ada di DB),
     * gunakan lahan pertama milik user, atau buat lahan default.
     */
    private function resolveLandId(array $det, int $userId): int
    {
        $landId = $det['land_id'] ?? 0;

        // Cek apakah land_id valid dan milik user
        if ($landId > 0) {
            $exists = Land::where('land_id', $landId)
                          ->where('user_id', $userId)
                          ->exists();
            if ($exists) {
                return $landId;
            }
        }

        // Fallback: ambil lahan pertama milik user
        $firstLand = Land::where('user_id', $userId)->first();
        if ($firstLand) {
            return $firstLand->land_id;
        }

        // Jika user belum punya lahan, buat satu default
        $defaultLand = Land::create([
            'user_id'  => $userId,
            'name'     => $det['land_name'] ?? 'Lahan Default (Pi)',
            'location' => 'Ditambahkan otomatis dari Raspberry Pi',
        ]);

        return $defaultLand->land_id;
    }

    /**
     * Simpan gambar base64 ke storage Laravel.
     * Return path relatif untuk disimpan di database.
     */
    private function saveImage(string $base64, $localId): string
    {
        $imageData = base64_decode($base64);
        $timestamp = now()->format('Ymd_His');
        $filename  = "detections/pi_{$timestamp}_{$localId}.jpg";

        Storage::disk('public')->put($filename, $imageData);

        return 'storage/' . $filename;
    }
}
