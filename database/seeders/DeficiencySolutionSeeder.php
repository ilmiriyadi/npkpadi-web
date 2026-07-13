<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NutrientDeficiency;
use App\Models\DeficiencySolution;

/**
 * Data solusi kekurangan hara padi berdasarkan pedoman Badan Litbang Pertanian
 * dan rekomendasi Permentan No. 40/2007 tentang Rekomendasi Pemupukan N, P, K
 * pada Padi Sawah Spesifik Lokasi.
 *
 * Fase pertumbuhan padi:
 *   - Vegetatif awal : 0-20 HST (pembentukan anakan)
 *   - Vegetatif aktif : 21-40 HST (anakan aktif, pertumbuhan tinggi)
 *   - Generatif awal : 41-60 HST (primordia bunga, bunting)
 *   - Generatif akhir: 61-80 HST (pembungaan, pengisian bulir)
 *   - Pemasakan      : 81-110 HST (pemasakan bulir hingga panen)
 *
 * Bibit Unggul: varietas hibrida/inbrida baru (Ciherang, IR64, Mekongga, dll)
 *   - Umur panen ~100-115 HST, respons pupuk tinggi
 * Bibit Lokal: varietas tradisional daerah
 *   - Umur panen ~120-150 HST, toleransi lebih baik tapi respons pupuk lebih rendah
 */
class DeficiencySolutionSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID dari tabel nutrient_deficiencies
        $nitrogen = NutrientDeficiency::where('name', 'like', '%Nitrogen%')->first();
        $fosfor   = NutrientDeficiency::where('name', 'like', '%Fosfor%')->first();
        $kalium   = NutrientDeficiency::where('name', 'like', '%Kalium%')->first();

        if (!$nitrogen || !$fosfor || !$kalium) {
            $this->command->error('Tabel nutrient_deficiencies belum terisi! Jalankan NutrientDeficiencySeeder terlebih dahulu.');
            return;
        }

        // ==========================================
        // Update saran umum di nutrient_deficiencies
        // ==========================================
        $nitrogen->update([
            'saran_umum_unggul' => 'Kekurangan Nitrogen (N) menyebabkan daun menguning dari ujung, pertumbuhan kerdil, dan anakan sedikit. '
                . 'Untuk varietas unggul (Ciherang, IR64, Mekongga, dll): aplikasikan Urea 200-250 kg/ha yang dibagi dalam 3 tahap pemupukan. '
                . 'Pemupukan I (7-14 HST): 1/3 dosis. Pemupukan II (21-28 HST): 1/3 dosis. Pemupukan III (35-42 HST): 1/3 dosis. '
                . 'Gunakan Bagan Warna Daun (BWD) untuk menentukan kebutuhan N secara presisi. '
                . 'Pupuk diberikan saat kondisi lahan macak-macak (lembab, tidak tergenang).',
            'saran_umum_lokal'  => 'Kekurangan Nitrogen (N) menyebabkan daun menguning dari ujung, pertumbuhan kerdil, dan anakan sedikit. '
                . 'Untuk varietas lokal: aplikasikan Urea 150-200 kg/ha yang dibagi dalam 2-3 tahap pemupukan. '
                . 'Pemupukan I (10-15 HST): 1/2 dosis. Pemupukan II (30-35 HST): 1/2 dosis. '
                . 'Varietas lokal umumnya kurang responsif terhadap pupuk N tinggi. Berikan pupuk organik (kompos/pupuk kandang) 2 ton/ha '
                . 'sebagai pelengkap untuk memperbaiki struktur tanah dan meningkatkan efisiensi pupuk anorganik.',
        ]);

        $fosfor->update([
            'saran_umum_unggul' => 'Kekurangan Fosfor (P) menyebabkan daun tua berwarna keunguan/kemerahan, perakaran dangkal, dan anakan terhambat. '
                . 'Untuk varietas unggul: aplikasikan SP-36 sebanyak 100-150 kg/ha ATAU TSP 75-100 kg/ha sebagai pupuk dasar (saat tanam). '
                . 'Fosfor bersifat immobil di tanah, sehingga HARUS diberikan sedini mungkin dan dibenamkan ke dalam tanah. '
                . 'Pada tanah masam (pH <5.5), berikan kapur dolomit 1-2 ton/ha minimal 2 minggu sebelum tanam untuk meningkatkan ketersediaan P.',
            'saran_umum_lokal'  => 'Kekurangan Fosfor (P) menyebabkan daun tua berwarna keunguan/kemerahan, perakaran dangkal, dan anakan terhambat. '
                . 'Untuk varietas lokal: aplikasikan SP-36 sebanyak 75-100 kg/ha sebagai pupuk dasar saat tanam. '
                . 'Varietas lokal memiliki sistem perakaran yang lebih adaptif, namun tetap memerlukan P terutama pada fase awal. '
                . 'Tambahkan pupuk organik (pupuk kandang 2-3 ton/ha) untuk meningkatkan ketersediaan P secara alami. '
                . 'Pada tanah masam, berikan kapur pertanian 1-1.5 ton/ha untuk meningkatkan pH tanah.',
        ]);

        $kalium->update([
            'saran_umum_unggul' => 'Kekurangan Kalium (K) menyebabkan ujung daun tua mengering kecoklatan, batang lemah mudah rebah, dan bulir hampa meningkat. '
                . 'Untuk varietas unggul: aplikasikan KCl 50-100 kg/ha yang dibagi 2 tahap. '
                . 'Pemupukan I (7-14 HST): 1/2 dosis bersama pupuk dasar. Pemupukan II (28-35 HST): 1/2 dosis bersama pemupukan susulan N. '
                . 'Kalium sangat penting untuk ketahanan terhadap hama penyakit dan kualitas gabah. '
                . 'Kembalikan jerami ke sawah setelah panen (~40% K tersimpan di jerami) untuk mengurangi kebutuhan pupuk K.',
            'saran_umum_lokal'  => 'Kekurangan Kalium (K) menyebabkan ujung daun tua mengering kecoklatan, batang lemah mudah rebah, dan bulir hampa meningkat. '
                . 'Untuk varietas lokal: aplikasikan KCl 50-75 kg/ha, cukup 1-2 kali aplikasi. '
                . 'Pemupukan I saat tanam (pupuk dasar). Pemupukan II pada 30-35 HST jika diperlukan. '
                . 'Varietas lokal umumnya lebih tahan rebah, namun K tetap penting untuk pengisian bulir. '
                . 'Praktik pengembalian jerami saat pengolahan tanah sangat dianjurkan sebagai sumber K alami.',
        ]);

        // ==========================================
        // Hapus data solutions lama, isi ulang
        // ==========================================
        DeficiencySolution::truncate();

        $solutions = [];

        // ==========================================
        // NITROGEN (N) - BIBIT UNGGUL
        // ==========================================
        $solutions[] = [
            'nutrient_deficiency_id' => $nitrogen->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 0,
            'max_hst' => 14,
            'solution_detail' => 'FASE PESEMAIAN & TANAM (0-14 HST): '
                . 'Aplikasikan pupuk dasar saat/sebelum tanam. Berikan Urea 75 kg/ha (1/3 dosis total) + SP-36 100 kg/ha + KCl 50 kg/ha. '
                . 'Pupuk dibenamkan ke dalam tanah saat pengolahan lahan terakhir atau ditabur merata 1-3 hari sebelum tanam. '
                . 'Pastikan lahan dalam kondisi macak-macak. Pada tanah yang sangat defisien N, tambahkan pupuk organik 2 ton/ha.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $nitrogen->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 15,
            'max_hst' => 30,
            'solution_detail' => 'FASE VEGETATIF AKTIF (15-30 HST): '
                . 'Berikan pupuk susulan I: Urea 75 kg/ha (1/3 dosis total) pada 21 HST. '
                . 'Periksa warna daun menggunakan Bagan Warna Daun (BWD). Jika warna daun di bawah panel 4, segera aplikasikan Urea. '
                . 'Tabur pupuk merata saat air macak-macak, hindari pemupukan saat genangan tinggi atau setelah hujan deras. '
                . 'Jika gejala kekurangan N sudah parah (daun sangat kuning), tambahkan Urea 25 kg/ha sebagai koreksi.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $nitrogen->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 31,
            'max_hst' => 50,
            'solution_detail' => 'FASE ANAKAN MAKSIMUM & PRIMORDIA (31-50 HST): '
                . 'Berikan pupuk susulan II: Urea 75 kg/ha (1/3 dosis total) pada 35-42 HST. '
                . 'Fase ini kritis karena menentukan jumlah malai per rumpun. '
                . 'Gunakan BWD: jika warna daun ≥ panel 4, pupuk N bisa dikurangi atau ditunda. '
                . 'Jika tanaman masih menunjukkan gejala defisiensi N (daun kuning pucat), berikan tambahan Urea 25-50 kg/ha. '
                . 'Hindari kelebihan N karena menyebabkan tanaman rentan rebah dan serangan hama.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $nitrogen->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 51,
            'max_hst' => 70,
            'solution_detail' => 'FASE GENERATIF / BUNTING & PEMBUNGAAN (51-70 HST): '
                . 'Pada fase ini, TIDAK dianjurkan menambah pupuk N dalam jumlah besar. '
                . 'Jika defisiensi N terdeteksi, berikan pupuk daun berkadar N tinggi (misal: pupuk daun Gandasil D) '
                . 'dengan konsentrasi 2 g/liter, semprotkan 2-3 kali interval 7 hari. '
                . 'Pemupukan urea tabur pada fase ini berisiko memperpanjang fase vegetatif dan menunda pembungaan. '
                . 'Pastikan pengairan berselang (intermittent irrigation) untuk meningkatkan efisiensi serapan N.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $nitrogen->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 71,
            'max_hst' => 110,
            'solution_detail' => 'FASE PEMASAKAN (71-110 HST): '
                . 'TIDAK direkomendasikan menambah pupuk N pada fase ini. '
                . 'Kelebihan N pada fase pemasakan menyebabkan: gabah hampa meningkat, kadar protein berlebih, '
                . 'dan kerentanan terhadap penyakit blast serta hawar daun bakteri (HDB). '
                . 'Jika daun bendera masih kuning, berikan pupuk daun dengan kandungan K dan P tinggi (Gandasil B) '
                . 'konsentrasi 2 g/liter sebagai koreksi. Kurangi pengairan secara bertahap menjelang panen.',
        ];

        // ==========================================
        // NITROGEN (N) - BIBIT LOKAL
        // ==========================================
        $solutions[] = [
            'nutrient_deficiency_id' => $nitrogen->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 0,
            'max_hst' => 15,
            'solution_detail' => 'FASE PESEMAIAN & TANAM (0-15 HST): '
                . 'Aplikasikan pupuk dasar: Urea 75-100 kg/ha (1/2 dosis total) + SP-36 75 kg/ha + KCl 50 kg/ha. '
                . 'Varietas lokal biasanya ditanam dengan jarak lebih lebar. '
                . 'Pupuk dibenamkan ke tanah saat olah tanah terakhir. '
                . 'Tambahkan pupuk kandang/kompos 2-3 ton/ha untuk memperbaiki kesuburan tanah secara berkelanjutan.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $nitrogen->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 16,
            'max_hst' => 35,
            'solution_detail' => 'FASE VEGETATIF AKTIF (16-35 HST): '
                . 'Berikan pupuk susulan: Urea 75-100 kg/ha (1/2 dosis total) pada 25-30 HST. '
                . 'Varietas lokal umumnya membentuk anakan lebih lambat dari varietas unggul. '
                . 'Jika gejala kekurangan N sudah terlihat (daun kuning dari ujung), segera aplikasikan Urea. '
                . 'Pertimbangkan pemberian MOL (Mikro Organisme Lokal) atau PGPR sebagai tambahan untuk meningkatkan fiksasi N alami.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $nitrogen->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 36,
            'max_hst' => 60,
            'solution_detail' => 'FASE ANAKAN MAKSIMUM & PRIMORDIA (36-60 HST): '
                . 'Jika defisiensi N terdeteksi pada fase ini, berikan Urea tambahan 25-50 kg/ha sebagai koreksi. '
                . 'Varietas lokal memiliki fase vegetatif lebih panjang (hingga 60 HST), jadi pemupukan susulan masih efektif. '
                . 'Gunakan BWD jika tersedia. Aplikasi pupuk saat lahan macak-macak (2-3 cm air). '
                . 'Hindari pemupukan saat hujan deras karena pupuk akan larut terbawa air.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $nitrogen->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 61,
            'max_hst' => 90,
            'solution_detail' => 'FASE GENERATIF / BUNTING & PEMBUNGAAN (61-90 HST): '
                . 'Tidak dianjurkan memberikan pupuk N tabur dalam jumlah besar. '
                . 'Jika defisiensi N terdeteksi, gunakan pupuk daun cair berkadar N tinggi, semprotkan 2-3 kali interval 7 hari. '
                . 'Varietas lokal pada fase ini fokus pada pembentukan dan pengisian bulir. '
                . 'Pastikan drainase baik untuk menghindari keracunan besi (Fe) yang menghambat serapan N.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $nitrogen->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 91,
            'max_hst' => 150,
            'solution_detail' => 'FASE PEMASAKAN (91-150 HST): '
                . 'TIDAK direkomendasikan menambah pupuk N. '
                . 'Varietas lokal berumur panjang (120-150 hari), fase pemasakan memerlukan pengeringan bertahap. '
                . 'Jika daun masih menguning, kemungkinan masalah bukan hanya N tapi juga drainase atau pH tanah. '
                . 'Aplikasikan pupuk daun berkadar K tinggi jika diperlukan. '
                . 'Hentikan pengairan 10-14 hari sebelum panen untuk mempercepat pemasakan.',
        ];

        // ==========================================
        // FOSFOR (P) - BIBIT UNGGUL
        // ==========================================
        $solutions[] = [
            'nutrient_deficiency_id' => $fosfor->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 0,
            'max_hst' => 14,
            'solution_detail' => 'FASE PESEMAIAN & TANAM (0-14 HST): '
                . 'KRITIS! Berikan SP-36 sebanyak 100-150 kg/ha SELURUHNYA sebagai pupuk dasar saat tanam. '
                . 'Fosfor harus diberikan sekaligus karena bersifat immobil di tanah (tidak mudah tercuci). '
                . 'Benamkan pupuk ke dalam tanah sedalam 5-10 cm. '
                . 'Pada tanah masam (pH <5.5): berikan kapur dolomit 1-2 ton/ha minimal 2 minggu sebelum tanam. '
                . 'Alternatif: gunakan NPK Phonska (15-15-15) 200-300 kg/ha jika SP-36 tidak tersedia.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $fosfor->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 15,
            'max_hst' => 30,
            'solution_detail' => 'FASE VEGETATIF AKTIF (15-30 HST): '
                . 'Jika gejala defisiensi P muncul pada fase ini (daun tua keunguan, pertumbuhan kerdil), '
                . 'artinya pemberian pupuk dasar P kurang optimal. '
                . 'Koreksi: berikan SP-36 tambahan 50 kg/ha dibenamkan di antara barisan tanaman. '
                . 'Atau semprotkan pupuk daun yang mengandung P tinggi (DAP 2-3 g/liter) setiap 5-7 hari. '
                . 'Periksa pH tanah - jika di bawah 5.0, segera berikan kapur pertanian.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $fosfor->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 31,
            'max_hst' => 50,
            'solution_detail' => 'FASE ANAKAN MAKSIMUM & PRIMORDIA (31-50 HST): '
                . 'Pemupukan P melalui tanah sudah TIDAK efektif pada fase ini karena akar sudah berkembang penuh. '
                . 'Jika defisiensi P terdeteksi, gunakan HANYA pupuk daun: '
                . 'Semprotkan larutan DAP (Diammonium Phosphate) 3-5 g/liter atau pupuk daun berkadar P tinggi. '
                . 'Frekuensi: 2-3 kali seminggu sekali. '
                . 'Fosfor pada fase ini penting untuk inisiasi pembentukan malai. '
                . 'Catat untuk musim tanam berikutnya: tingkatkan dosis SP-36 pupuk dasar.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $fosfor->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 51,
            'max_hst' => 70,
            'solution_detail' => 'FASE GENERATIF / BUNTING & PEMBUNGAAN (51-70 HST): '
                . 'Defisiensi P pada fase ini berdampak serius: malai pendek, jumlah gabah per malai berkurang. '
                . 'Koreksi melalui pupuk daun: semprotkan pupuk daun MKP (Mono Kalium Phosphate) 5 g/liter '
                . 'atau pupuk daun Gandasil B (tinggi P dan K) 2 g/liter, setiap 5-7 hari selama 2-3 kali. '
                . 'Pastikan penyemprotan dilakukan pagi hari (06.00-09.00) atau sore (15.00-17.00) untuk penyerapan optimal.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $fosfor->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 71,
            'max_hst' => 110,
            'solution_detail' => 'FASE PEMASAKAN (71-110 HST): '
                . 'Koreksi defisiensi P sangat terbatas pada fase ini. '
                . 'Jika gejala masih terlihat, semprotkan pupuk daun MKP 3-5 g/liter maksimal 2 kali aplikasi. '
                . 'Utamakan pengelolaan air: pertahankan kondisi macak-macak untuk membantu ketersediaan P. '
                . 'PENTING untuk musim tanam berikutnya: lakukan pengapuran 2 ton/ha dan tingkatkan pupuk dasar SP-36 '
                . 'menjadi 150 kg/ha. Kembalikan jerami ke lahan sebagai sumber P organik.',
        ];

        // ==========================================
        // FOSFOR (P) - BIBIT LOKAL
        // ==========================================
        $solutions[] = [
            'nutrient_deficiency_id' => $fosfor->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 0,
            'max_hst' => 15,
            'solution_detail' => 'FASE PESEMAIAN & TANAM (0-15 HST): '
                . 'Berikan SP-36 sebanyak 75-100 kg/ha SELURUHNYA sebagai pupuk dasar. '
                . 'Benamkan ke tanah sedalam 5-10 cm saat olah tanah terakhir. '
                . 'Varietas lokal memiliki perakaran yang lebih dalam, sehingga pembenaman pupuk P penting. '
                . 'Tambahkan pupuk kandang 2-3 ton/ha yang mengandung P organik. '
                . 'Pada tanah masam: kapur dolomit 1-1.5 ton/ha, 2 minggu sebelum tanam.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $fosfor->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 16,
            'max_hst' => 35,
            'solution_detail' => 'FASE VEGETATIF AKTIF (16-35 HST): '
                . 'Jika gejala defisiensi P muncul (warna keunguan di daun tua, anakan lambat): '
                . 'Berikan SP-36 tambahan 50 kg/ha dibenamkan di dekat perakaran. '
                . 'Atau semprotkan pupuk daun DAP 2-3 g/liter setiap minggu selama 2-3 kali. '
                . 'Varietas lokal tumbuh lebih lambat, jadi defisiensi P mungkin baru terlihat pada 20-25 HST. '
                . 'Pemberian pupuk hayati (mikorhiza) sangat membantu penyerapan P pada varietas lokal.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $fosfor->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 36,
            'max_hst' => 60,
            'solution_detail' => 'FASE ANAKAN MAKSIMUM & PRIMORDIA (36-60 HST): '
                . 'Pemupukan P lewat tanah tidak efektif pada fase ini. '
                . 'Koreksi melalui pupuk daun: DAP 3-5 g/liter atau pupuk daun tinggi P, '
                . 'semprotkan 2-3 kali interval 7 hari. '
                . 'Varietas lokal memiliki fase vegetatif lebih panjang, primordia terbentuk sekitar 55-60 HST. '
                . 'P sangat dibutuhkan untuk pembentukan akar baru dan inisiasi malai.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $fosfor->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 61,
            'max_hst' => 90,
            'solution_detail' => 'FASE GENERATIF (61-90 HST): '
                . 'Gunakan pupuk daun MKP 5 g/liter atau Gandasil B 2 g/liter, semprotkan 2-3 kali. '
                . 'Defisiensi P pada fase ini mengurangi jumlah bulir per malai. '
                . 'Pastikan drainase sawah baik - genangan berlebih mengurangi ketersediaan P. '
                . 'Semprotkan pagi hari untuk penyerapan optimal melalui stomata daun.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $fosfor->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 91,
            'max_hst' => 150,
            'solution_detail' => 'FASE PEMASAKAN (91-150 HST): '
                . 'Koreksi P sangat terbatas. Semprotkan pupuk daun MKP 3 g/liter maksimal 2 kali jika diperlukan. '
                . 'Fokuskan pengelolaan air: pengeringan bertahap untuk pemasakan optimal. '
                . 'CATATAN UNTUK MUSIM BERIKUTNYA: tingkatkan pupuk dasar SP-36 menjadi 100-125 kg/ha, '
                . 'berikan pengapuran 1.5-2 ton/ha, dan tambahkan pupuk organik 3 ton/ha. '
                . 'Kembalikan jerami ke sawah setelah panen.',
        ];

        // ==========================================
        // KALIUM (K) - BIBIT UNGGUL
        // ==========================================
        $solutions[] = [
            'nutrient_deficiency_id' => $kalium->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 0,
            'max_hst' => 14,
            'solution_detail' => 'FASE PESEMAIAN & TANAM (0-14 HST): '
                . 'Berikan KCl 50 kg/ha (1/2 dosis total) sebagai pupuk dasar bersama SP-36 dan Urea. '
                . 'Tabur merata dan benamkan ke tanah saat pengolahan lahan. '
                . 'Pada lahan yang baru dibuka atau tanah berpasir: tingkatkan dosis menjadi 75 kg/ha. '
                . 'Alternatif: gunakan NPK Phonska (15-15-15) 200-300 kg/ha yang sudah mengandung K. '
                . 'Jangan bersamaan dengan pengapuran - beri jarak minimal 1 minggu.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $kalium->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 15,
            'max_hst' => 30,
            'solution_detail' => 'FASE VEGETATIF AKTIF (15-30 HST): '
                . 'Jika gejala defisiensi K muncul (ujung daun tua mengering kecoklatan, tepi daun nekrosis): '
                . 'Berikan KCl 25-50 kg/ha sebagai koreksi, tabur di antara barisan tanaman. '
                . 'Semprotkan pupuk daun KNO3 (Kalium Nitrat) 5 g/liter sebagai tindakan cepat. '
                . 'K penting untuk ketahanan tanaman terhadap penyakit (blast, HDB) yang sering menyerang pada fase ini.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $kalium->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 31,
            'max_hst' => 50,
            'solution_detail' => 'FASE ANAKAN MAKSIMUM & PRIMORDIA (31-50 HST): '
                . 'Berikan pupuk susulan K: KCl 50 kg/ha (1/2 dosis total) pada 28-35 HST. '
                . 'Jika defisiensi K terdeteksi, tambahkan KCl 25 kg/ha. '
                . 'K pada fase ini menentukan kekuatan batang (tahan rebah) dan ketahanan terhadap hama penggerek batang. '
                . 'Semprotkan pupuk daun MKP (Mono Kalium Phosphate) 5 g/liter sebagai koreksi cepat. '
                . 'Pertahankan kondisi lahan macak-macak saat pemupukan.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $kalium->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 51,
            'max_hst' => 70,
            'solution_detail' => 'FASE GENERATIF / BUNTING & PEMBUNGAAN (51-70 HST): '
                . 'Defisiensi K pada fase ini SANGAT kritis: menyebabkan gabah hampa tinggi dan kualitas gabah rendah. '
                . 'Koreksi: semprotkan KCl 1-2% (10-20 g/liter) atau pupuk daun MKP 5 g/liter, '
                . '2-3 kali interval 5-7 hari. '
                . 'K berperan dalam translokasi fotosintat ke bulir. '
                . 'Berikan KCl tabur 25 kg/ha jika defisiensi parah dan tanaman masih dalam fase bunting awal. '
                . 'Pastikan pengairan berselang untuk meningkatkan serapan K.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $kalium->nutrient_deficiency_id,
            'seed_type' => 'unggul',
            'min_hst' => 71,
            'max_hst' => 110,
            'solution_detail' => 'FASE PEMASAKAN (71-110 HST): '
                . 'K masih berperan penting dalam pengisian dan pemasakan bulir. '
                . 'Jika defisiensi K terdeteksi, semprotkan pupuk daun MKP 5 g/liter atau KCl 1% (10 g/liter), '
                . 'maksimal 2 kali aplikasi. '
                . 'Kekurangan K pada fase ini menyebabkan: gabah tidak penuh, rendemen giling rendah, '
                . 'dan butir patah (broken rice) meningkat. '
                . 'UNTUK MUSIM BERIKUTNYA: kembalikan seluruh jerami ke sawah (mengandung ~40% K total tanaman) '
                . 'dan tingkatkan dosis KCl menjadi 100 kg/ha.',
        ];

        // ==========================================
        // KALIUM (K) - BIBIT LOKAL
        // ==========================================
        $solutions[] = [
            'nutrient_deficiency_id' => $kalium->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 0,
            'max_hst' => 15,
            'solution_detail' => 'FASE PESEMAIAN & TANAM (0-15 HST): '
                . 'Berikan KCl 50 kg/ha sebagai pupuk dasar, tabur merata dan benamkan ke tanah. '
                . 'Varietas lokal memiliki batang yang umumnya lebih kuat, tapi K tetap penting untuk ketahanan penyakit. '
                . 'Pada tanah berpasir atau gambut: tingkatkan dosis menjadi 75 kg/ha. '
                . 'Kombinasikan dengan pengembalian jerami atau pemberian abu sekam 500 kg/ha sebagai sumber K alami.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $kalium->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 16,
            'max_hst' => 35,
            'solution_detail' => 'FASE VEGETATIF AKTIF (16-35 HST): '
                . 'Jika gejala defisiensi K muncul: berikan KCl 25 kg/ha sebagai koreksi. '
                . 'Semprotkan pupuk daun KNO3 5 g/liter sebagai tindakan cepat, 2 kali interval 7 hari. '
                . 'Varietas lokal biasanya lebih toleran terhadap kekurangan K ringan. '
                . 'Jika menggunakan pupuk organik (pupuk kandang), sebagian kebutuhan K sudah terpenuhi. '
                . 'Periksa juga kemungkinan keracunan Fe pada tanah masam yang menghambat serapan K.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $kalium->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 36,
            'max_hst' => 60,
            'solution_detail' => 'FASE ANAKAN MAKSIMUM & PRIMORDIA (36-60 HST): '
                . 'Berikan pupuk susulan K: KCl 25-50 kg/ha pada 35-40 HST jika belum diberikan. '
                . 'Jika defisiensi K terdeteksi, semprotkan MKP 5 g/liter atau KCl 1% (10 g/liter). '
                . 'K pada fase ini menentukan kekuatan batang menjelang fase generatif. '
                . 'Varietas lokal yang berbatang tinggi sangat memerlukan K cukup agar tidak rebah saat berbuah. '
                . 'Abu jerami/sekam merupakan sumber K cepat tersedia yang dapat ditaburkan di sekitar rumpun.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $kalium->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 61,
            'max_hst' => 90,
            'solution_detail' => 'FASE GENERATIF (61-90 HST): '
                . 'Defisiensi K menyebabkan gabah hampa dan kualitas rendah. '
                . 'Semprotkan pupuk daun MKP 5 g/liter atau KCl 1-2%, 2-3 kali interval 5-7 hari. '
                . 'Varietas lokal pada fase ini sangat rentan rebah jika K kurang karena batang tinggi dan malai berat. '
                . 'Pastikan pengairan berselang untuk meningkatkan serapan K. '
                . 'Hindari genangan tinggi yang menyebabkan kehilangan K melalui pencucian.',
        ];
        $solutions[] = [
            'nutrient_deficiency_id' => $kalium->nutrient_deficiency_id,
            'seed_type' => 'lokal',
            'min_hst' => 91,
            'max_hst' => 150,
            'solution_detail' => 'FASE PEMASAKAN (91-150 HST): '
                . 'Semprotkan pupuk daun MKP 3-5 g/liter jika defisiensi K masih terlihat, maksimal 2 kali. '
                . 'K berperan dalam kualitas dan bobot gabah. '
                . 'Varietas lokal berumur panjang, sehingga kebutuhan K total lebih besar. '
                . 'UNTUK MUSIM BERIKUTNYA: kembalikan jerami ke sawah, berikan abu sekam 500-1000 kg/ha, '
                . 'dan tingkatkan dosis KCl pupuk dasar menjadi 75 kg/ha. '
                . 'Hindari membakar jerami - ini menghilangkan N dan S serta merusak mikroorganisme tanah.',
        ];

        // Insert semua data
        foreach ($solutions as $sol) {
            DeficiencySolution::create(array_merge($sol, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Data deficiency_solutions berhasil diisi: ' . count($solutions) . ' record.');
        $this->command->info('  - Nitrogen (N): 10 record (5 unggul + 5 lokal)');
        $this->command->info('  - Fosfor (P): 10 record (5 unggul + 5 lokal)');
        $this->command->info('  - Kalium (K): 10 record (5 unggul + 5 lokal)');
    }
}
