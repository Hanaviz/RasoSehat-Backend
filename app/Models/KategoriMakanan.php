<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriMakanan extends Model
{
    use HasFactory;

    // ✅ Pastikan nama tabel sama persis dengan di database
    protected $table = 'kategori_makanan';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    /**
     * Relasi ke model MenuMakanan.
     * Satu kategori bisa memiliki banyak menu makanan.
     */
    protected $with = ['menuMakanan'];
    public function menuMakanan()
    {
        return $this->hasMany(MenuMakanan::class, 'kategori_id');
    }
}
