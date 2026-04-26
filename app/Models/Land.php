<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Land extends Model
{
    use HasFactory;

    // Beri tahu Laravel nama Primary Key-mu
    protected $primaryKey = 'land_id';

    // Kolom yang boleh diisi
    protected $fillable = [
        'user_id',
        'name',
        'location',
    ];

    // Relasi: Lahan ini milik 1 User (Petani)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi: Lahan ini punya banyak Riwayat Deteksi
    public function detections()
    {
        return $this->hasMany(Detection::class, 'land_id', 'land_id');
    }
}