<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restoran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_restoran',
        'nama_pemilik',
        'deskripsi',
        'kategori_toko', // atau kategori_id
        'alamat',
        'latitude',
        'longitude',
        'no_telepon',
        'no_whatsapp',
        'jam_operasional',
        'media_sosial',
        'jenis_usaha',
        'status_verifikasi',
        'verifikasi_ktp',
        'verifikasi_npwp',
        'verifikasi_nib_siup',
        'verifikasi_akta_pendirian'
    ];

    protected $casts = [
        'jam_operasional' => 'array',
        'media_sosial' => 'array',
    ];

    protected $with = ['ulasan'];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke menu makanan
    public function menuMakanan()
    {
        return $this->hasMany(MenuMakanan::class);
    }

    // Relasi ke ulasan
    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'restoran_id');
    }

    // Relasi ke notifikasi
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class);
    }

    // Relasi ke kategori (jika tabel kategori ada)
    public function kategori()
    {
        return $this->belongsTo(KategoriMakanan::class, 'kategori_toko', 'id');
    }
}
