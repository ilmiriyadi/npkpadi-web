<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutrientDeficiency extends Model
{
    use HasFactory;

    protected $primaryKey = 'nutrient_deficiency_id';

    protected $fillable = [
        'name',
        'solution',
    ];

    // Relasi: Penyakit ini bisa muncul di banyak Deteksi
    public function detections()
    {
        return $this->hasMany(Detection::class, 'nutrient_deficiency_id', 'nutrient_deficiency_id');
    }
}