<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeficiencySolution extends Model
{
    // Kolom yang boleh diisi
    protected $fillable = [
        'nutrient_deficiency_id', 
        'seed_type', 
        'min_hst', 
        'max_hst', 
        'solution_detail'
    ];

    // Relasi balik ke tabel induk penyakit
    public function nutrientDeficiency()
    {
        return $this->belongsTo(NutrientDeficiency::class, 'nutrient_deficiency_id', 'nutrient_deficiency_id');
    }
}