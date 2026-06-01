<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NutrientDeficiencySeeder extends Seeder
{
    public function run()
    {
        // 1. Matikan pengecekan relasi sementara
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel
        DB::table('nutrient_deficiencies')->truncate();

        // 3. Nyalakan kembali pengecekan relasi
        Schema::enableForeignKeyConstraints();

        DB::table('nutrient_deficiencies')->insert([
            [
                'name' => 'Defisiensi Nitrogen (N)',
                'solution' => 'Sistem mendeteksi indikasi visual kekurangan N. Lakukan pemupukan susulan Urea.',
                'solution_vegetative' => 'Indikasi daun menguning terdeteksi. Segera berikan pupuk susulan Urea. Sebagai referensi Kementan: pemupukan N dilakukan pada umur 7-10 HST (40%), 21-25 HST (40%), dan sisanya di 30-35 HST hanya jika daun masih kurang hijau.',
                'solution_generative' => 'Gunakan hasil AI ini sebagai panduan (seperti Bagan Warna Daun). Jika daun terdeteksi kuning, berikan pupuk Urea secukupnya. HINDARI pupuk berlebih agar tanaman tidak lunak dan rentan hama.',
                'solution_ripening' => 'TIDAK DIREKOMENDASIKAN memberikan pupuk Nitrogen pada fase ini walaupun daun terlihat kuning. Pemberian N saat pengisian gabah akan membuat padi rentan roboh dan terserang jamur.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Defisiensi Fosfor (P)',
                'solution' => 'Sistem mendeteksi indikasi visual kekurangan P. Periksa kondisi tanah dan berikan pupuk majemuk.',
                'solution_vegetative' => 'Terdeteksi kekurangan P di fase awal tumbuh. Disarankan segera mengaplikasikan pupuk SP-36/NPK. Tambahkan pupuk organik (kompos/kandang) untuk memperbaiki struktur tanah yang memicu defisiensi ini.',
                'solution_generative' => 'Pemberian pupuk P susulan di fase ini kurang efektif karena unsur P lambat diserap. Disarankan menggunakan pupuk daun majemuk yang disemprotkan untuk penanganan darurat.',
                'solution_ripening' => 'Tidak perlu penanganan pupuk kimia. Fokus pada pengairan. Catat lahan ini agar pada musim tanam berikutnya diberikan pupuk organik dan unsur P yang cukup di awal tanam.',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Defisiensi Kalium (K)',
                'solution' => 'Sistem mendeteksi indikasi visual kekurangan K. Lakukan penambahan pupuk KCl.',
                'solution_vegetative' => 'Terdeteksi kekurangan K. Sangat disarankan menambahkan pupuk KCl serta mengembalikan jerami sisa panen ke lahan untuk memperkuat batang agar tidak mudah roboh.',
                'solution_generative' => 'Segera berikan pupuk KCl susulan! Unsur K sangat krusial di fase bunting ini agar proses pengisian gabah menjadi padat, bernas, dan tidak hampa.',
                'solution_ripening' => 'Penanganan dengan pupuk padat sudah terlambat. Jaga ketersediaan air dengan sistem pengairan berselang. Jika kondisi sangat parah, pertimbangkan penyemprotan pupuk Kalium cair via daun.',
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);
    }
}