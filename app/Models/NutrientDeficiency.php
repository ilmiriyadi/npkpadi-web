<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutrientDeficiency extends Model
{
    // Beritahu Laravel nama ID kustom kita
    protected $primaryKey = 'nutrient_deficiency_id';
    
    // Kolom yang boleh diisi
    protected $fillable = [
        'name', 
        'saran_umum_unggul', 
        'saran_umum_lokal'
    ];

    // Relasi ke tabel solusi (fase umur)
    public function solutions()
    {
        return $this->hasMany(DeficiencySolution::class, 'nutrient_deficiency_id', 'nutrient_deficiency_id');
    }
}