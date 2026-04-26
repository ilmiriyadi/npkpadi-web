<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detection extends Model
{
    use HasFactory;

    protected $primaryKey = 'detection_id';

    protected $fillable = [
        'land_id',
        'nutrient_deficiency_id',
        'image_path',
        'confidence_score',
        'is_synced',
    ];

    // Relasi: Deteksi ini terjadi di 1 Lahan
    public function land()
    {
        return $this->belongsTo(Land::class, 'land_id', 'land_id');
    }

    // Relasi: Deteksi ini menghasilkan 1 Kesimpulan Penyakit
    public function nutrientDeficiency()
    {
        return $this->belongsTo(NutrientDeficiency::class, 'nutrient_deficiency_id', 'nutrient_deficiency_id');
    }
}