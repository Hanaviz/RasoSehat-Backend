<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuMakanan extends Model
{
    use HasFactory;

    protected $table = 'menu_makanan';

    protected $with = ['ulasan'];

    protected $fillable = [
        'restoran_id',
        'kategori_id',
        'nama_menu',
        'deskripsi',
        'kalori',
        'protein',
        'gula',
        'lemak',
        'harga',
        'foto',
        'status_verifikasi',
    ];

    // ===============================
    // 🔗 RELASI MODEL
    // ===============================

    public function restoran()
    {
        return $this->belongsTo(Restoran::class, 'restoran_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriMakanan::class, 'kategori_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'menu_makanan_id');
    }

    public function verifikasi()
    {
        return $this->hasOne(Verifikasi::class, 'objek_id')
                    ->where('tipe_objek', 'menu');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'menu_id');
    }

    // ===============================
    // 🧠 FUNGSI TAMBAHAN
    // ===============================

    public function getAverageRatingAttribute()
    {
        return $this->ulasan()->avg('rating') ?? 0;
    }

    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
