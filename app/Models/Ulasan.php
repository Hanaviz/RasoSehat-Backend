<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    use HasFactory;

    protected $table = 'ulasans'; // bisa disesuaikan jika tabel kamu bernama 'ulasan'

    protected $with = ['user', 'restoran', 'menuMakanan'];
    
    protected $fillable = [
        'user_id',
        'restoran_id',
        'menu_makanan_id',
        'rating',
        'komentar',
    ];

    /**
     * Relasi ke User (Many-to-One)
     * Setiap ulasan dibuat oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Restoran (Many-to-One)
     * Setiap ulasan bisa ditujukan untuk satu restoran.
     */
    public function restoran()
    {
        return $this->belongsTo(Restoran::class, 'restoran_id');
    }

    /**
     * Relasi ke MenuMakanan (Many-to-One)
     * Jika ulasan ditujukan untuk makanan tertentu.
     */
    public function menuMakanan()
    {
        return $this->belongsTo(MenuMakanan::class, 'menu_makanan_id');
    }
}
