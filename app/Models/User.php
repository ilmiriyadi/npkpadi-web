<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi: 1 User (Petani) bisa punya banyak Lahan
     */
    public function lands()
    {
        // Parameter: (NamaModel, foreign_key_di_tabel_lands, local_key_di_tabel_users)
        return $this->hasMany(Land::class, 'user_id', 'user_id');
    }

    /**
     * Relasi: 1 User (Petani) bisa punya banyak Deteksi melalui Lahan
     */
    public function detections()
    {
        return $this->hasManyThrough(Detection::class, Land::class, 'user_id', 'land_id', 'user_id', 'land_id');
    }
}
