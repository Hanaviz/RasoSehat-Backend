<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restoran extends Model
{
    use HasFactory;
    
    // Perhatikan nama tabel (defaultnya 'restorans')
    // protected $table = 'restorans'; 

    protected $fillable = [
        'user_id',
        'nama_restoran',
        'nama_pemilik',
        'deskripsi',
        'kategori_toko', 
        'alamat',
        'latitude',
        'longitude',
        'no_telepon',
        'no_whatsapp',
        'jam_operasional',
        'media_sosial',
        'jenis_usaha',
        'status_verifikasi',
        
        // 💡 KOLOM BARU DARI FORM AUDIT LITE (PENTING!)
        'health_focus',
        'dominant_fat',
        'dominant_cooking_method',
        'sales_channels',
        
        // DOKUMEN VERIFIKASI (Path file)
        'verifikasi_ktp',
        'verifikasi_npwp',
        'verifikasi_nib_siup',
        'verifikasi_akta_pendirian'
    ];

    // 💡 CASTING: Digunakan untuk mengubah tipe data saat disimpan/ditarik dari DB
    protected $casts = [
        // Asumsi data ini disimpan sebagai string JSON Array (multi-pilihan)
        // Jika data dikirim dari frontend sebagai string tunggal (single value), hapus casting array
        'jam_operasional' => 'string', // Dibiarkan string, karena formatnya bebas
        'media_sosial' => 'string', // Dibiarkan string
        
        // Jika klaim fokus kesehatan (health_focus) di form adalah multi-pilihan:
        // 'health_focus' => 'array', 

        // Jika Anda ingin latitude/longitude selalu ditarik sebagai float/decimal
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // protected $with = ['ulasan']; // Dihapus, panggil secara eksplisit di controller

    // =========================================================
    // 🔗 RELASI MODEL
    // =========================================================

    // Relasi ke User (One to One)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Menu Makanan (One to Many)
    public function menus() // Diganti 'menuMakanan' agar sesuai standar Laravel (Jamak)
    {
        return $this->hasMany(MenuMakanan::class, 'restoran_id');
    }

    // Relasi ke ulasan (One to Many)
    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'restoran_id');
    }

    // Relasi ke notifikasi (One to Many)
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'restoran_id');
    }

    // Relasi ke kategori (Disarankan agar kategori_toko menyimpan ID)
    public function kategori()
    {
        // 💡 Asumsi: Kategori toko disimpan sebagai STRING NAMA KATEGORI
        // Jika ingin berelasi ke tabel kategori, field kategori_toko harus menyimpan KATEGORI ID
        // Jika kategori_toko adalah STRING: Relasi ini tidak akan berfungsi sebagai belongsTo normal.
        
        // Jika Anda yakin kategori_toko adalah ID:
        // return $this->belongsTo(KategoriMakanan::class, 'kategori_toko', 'id');
        
        // Untuk saat ini, kita biarkan sebagai string di database.
        return null; // Dihapus sementara karena kemungkinan kategori_toko adalah string.
    }
}