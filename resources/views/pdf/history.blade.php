<!DOCTYPE html>
<html>
<head>
    <title>Laporan Riwayat Deteksi NPK</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #387F39; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #2d3748; }
        .header p { margin: 5px 0 0 0; color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; vertical-align: top; }
        th { background-color: #f0fff4; color: #276749; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; color: #fff;}
        .bg-unggul { background-color: #3182ce; }
        .bg-lokal { background-color: #805ad5; }
        .text-danger { color: #e53e3e; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Riwayat Deteksi Nutrisi Padi (NPK)</h2>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA</p>
        @if(isset($totalDetections, $pdfLimit) && $totalDetections > $detections->count())
            <p>Menampilkan {{ $detections->count() }} data terbaru dari {{ $totalDetections }} data. Gunakan filter untuk laporan lebih spesifik.</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 20%;">Informasi Lahan</th>
                <th style="width: 15%;">Waktu Deteksi</th>
                <th style="width: 15%;">Umur Padi</th>
                <th style="width: 15%;">Penyakit / Hasil</th>
                <th style="width: 30%;">Saran Penanganan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detections as $index => $detection)
                @php
                    $land = $detection->land;
                    $deficiency = $detection->nutrientDeficiency;
                    $seedType = $land->seed_type ?? 'unggul';
                    $batasPanen = ($seedType == 'unggul') ? 110 : 270;

                    if (!$land || !$land->planting_date || !$deficiency) {
                        $hst = '-';
                        $teksSolusi = 'Data lahan atau hasil deteksi tidak lengkap.';
                        $isError = true;
                    } else {
                        $rawDays = \Carbon\Carbon::parse($land->planting_date)->diffInDays($detection->created_at);
                        $hst = intval($rawDays);

                        if ($hst > $batasPanen) {
                            $teksSolusi = "PERINGATAN: Umur padi melebihi batas panen (" . $batasPanen . " HST). Harap perbarui tanggal tanam.";
                            $isError = true;
                        } else {
                            $isError = false;
                            $solusi = $deficiency->solutions
                                ->first(fn ($solution) => $solution->seed_type == $seedType && $solution->min_hst <= $hst && $solution->max_hst >= $hst);
                            $teksSolusi = $solusi ? "[Fase {$solusi->min_hst}-{$solusi->max_hst} HST]\n" . $solusi->solution_detail : "Saran Umum:\n" . (($seedType == 'unggul') ? $deficiency->saran_umum_unggul : $deficiency->saran_umum_lokal);
                        }
                    }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $land->name ?? 'Lahan tidak tersedia' }}</strong><br>
                        @if($land?->user)
                            Pemilik: {{ $land->user->name }}<br>
                        @endif
                        <span class="badge {{ $seedType == 'unggul' ? 'bg-unggul' : 'bg-lokal' }}" style="margin-top: 5px;">
                            {{ ucfirst($seedType) }}
                        </span>
                    </td>
                    <td>{{ $detection->created_at->format('d M Y') }}<br>{{ $detection->created_at->timezone('Asia/Makassar')->format('H:i') }} WITA</td>
                    <td class="{{ $isError ? 'text-danger' : '' }}">{{ $hst }} HST</td>
                    <td><strong>{{ $deficiency->name ?? 'Tidak tersedia' }}</strong><br>Akurasi: {{ round($detection->confidence_score, 1) }}%</td>
                    <td style="white-space: pre-wrap;">{{ $teksSolusi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data riwayat deteksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
