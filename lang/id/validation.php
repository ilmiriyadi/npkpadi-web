<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa Validasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut berisi pesan error default yang digunakan oleh
    | kelas validator. Kamu bebas mengubah pesan-pesan ini.
    |
    */

    'accepted'        => 'Kolom :attribute harus disetujui.',
    'active_url'      => 'Kolom :attribute bukan URL yang valid.',
    'after'           => 'Kolom :attribute harus berupa tanggal setelah :date.',
    'alpha'           => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash'      => 'Kolom :attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num'       => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'array'           => 'Kolom :attribute harus berupa sebuah array.',
    'before'          => 'Kolom :attribute harus berupa tanggal sebelum :date.',
    'between'         => [
        'numeric' => 'Kolom :attribute harus antara :min dan :max.',
        'file'    => 'Kolom :attribute harus antara :min dan :max kilobyte.',
        'string'  => 'Kolom :attribute harus antara :min dan :max karakter.',
        'array'   => 'Kolom :attribute harus memiliki antara :min dan :max item.',
    ],
    'boolean'         => 'Kolom :attribute harus bernilai true atau false.',
    'confirmed'       => 'Konfirmasi :attribute tidak cocok.',
    'date'            => 'Kolom :attribute bukan tanggal yang valid.',
    'digits'          => 'Kolom :attribute harus berupa :digits angka.',
    'digits_between'  => 'Kolom :attribute harus antara angka :min dan :max.',
    'email'           => 'Kolom :attribute harus berupa alamat email yang valid.',
    'exists'          => 'Pilihan :attribute tidak valid.',
    'file'            => 'Kolom :attribute harus berupa sebuah file.',
    'image'           => 'Kolom :attribute harus berupa gambar (jpeg, png, bmp, gif, svg, atau webp).',
    'in'              => 'Pilihan :attribute tidak valid.',
    'integer'         => 'Kolom :attribute harus berupa bilangan bulat.',
    'lowercase'       => 'Kolom :attribute harus menggunakan huruf kecil semua.',
    'max'             => [
        'numeric' => 'Kolom :attribute tidak boleh lebih dari :max.',
        'file'    => 'Kolom :attribute tidak boleh lebih dari :max kilobyte.',
        'string'  => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
        'array'   => 'Kolom :attribute tidak boleh memiliki lebih dari :max item.',
    ],
    'mimes'           => 'Kolom :attribute harus berupa file berjenis: :values.',
    'min'             => [
        'numeric' => 'Kolom :attribute minimal harus :min.',
        'file'    => 'Kolom :attribute minimal harus :min kilobyte.',
        'string'  => 'Kolom :attribute minimal harus :min karakter.',
        'array'   => 'Kolom :attribute minimal harus memiliki :min item.',
    ],
    'numeric'         => 'Kolom :attribute harus berupa angka.',
    'password'        => [
        'letters' => 'Kolom :attribute harus memiliki setidaknya satu huruf.',
        'mixed'   => 'Kolom :attribute harus memiliki setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers' => 'Kolom :attribute harus memiliki setidaknya satu angka.',
        'symbols' => 'Kolom :attribute harus memiliki setidaknya satu simbol.',
    ],
    'required'        => 'Kolom :attribute wajib diisi.',
    'same'            => 'Kolom :attribute dan :other harus sama.',
    'size'            => [
        'numeric' => 'Kolom :attribute harus berukuran :size.',
        'file'    => 'Kolom :attribute harus berukuran :size kilobyte.',
        'string'  => 'Kolom :attribute harus berukuran :size karakter.',
        'array'   => 'Kolom :attribute harus mengandung :size item.',
    ],
    'string'          => 'Kolom :attribute harus berupa teks.',
    'unique'          => ':attribute ini sudah terdaftar di sistem.',
    'uploaded'        => 'Kolom :attribute gagal diunggah.',
    'url'             => 'Format :attribute tidak valid.',
    'uppercase'       => 'Kolom :attribute harus menggunakan huruf besar semua.',

    /*
    |--------------------------------------------------------------------------
    | Kustomisasi Nama Atribut
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan untuk menukar 'placeholder' atribut kita
    | dengan sesuatu yang lebih ramah pembaca seperti "Alamat Email" 
    | alih-alih "email".
    |
    */

    'attributes' => [
        'name'     => 'Nama Lengkap',
        'email'    => 'Alamat Email',
        'password' => 'Kata Sandi',
        'image'    => 'Foto Daun',
        'land_id'  => 'ID Sawah',
    ],

];