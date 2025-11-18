<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // Diperlukan untuk Accessor/Mutator

class MenuMakanan extends Model
{
    use HasFactory;

    protected $table = 'menu_makanan';

    protected $fillable = [
        'restoran_id',
        'kategori_id',
        'nama_menu',
        'deskripsi',
        'kalori',
        'protein',
        'gula',
        'lemak',
        'serat', 
        'lemak_jenuh', 
        'harga',
        'foto',
        'status_verifikasi',
        'bahan_baku', 
        'metode_masak', 
        'diet_claims', 
        'catatan_admin', 
    ];

    // 💡 CASTING: Diet claims harus disimpan sebagai JSON Array
    protected $casts = [
        'diet_claims' => 'array',
        'kalori' => 'integer',
        'protein' => 'float',
        'gula' => 'float',
        'lemak' => 'float',
        'serat' => 'float',
        'lemak_jenuh' => 'float', 
    ];

    // ===============================
    // 🔗 RELASI MODEL
    // ===============================

    /**
     * Hubungan Menu Makanan ke Restoran (Many to One).
     */
    public function restoran()
    {
        return $this->belongsTo(Restoran::class, 'restoran_id');
    }

    /**
     * Hubungan Menu Makanan ke Kategori (Many to One).
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriMakanan::class, 'kategori_id');
    }
    
    /**
     * Hubungan Menu Makanan ke Ulasan (One to Many).
     */
    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'menu_makanan_id');
    }

    /**
     * Hubungan Menu Makanan ke Log Verifikasi (One to One/Polymorphic).
     * Diasumsikan tabel 'verifikasi' menggunakan kolom objek_id dan tipe_objek
     */
    public function verifikasi()
    {
        // Catatan: Pastikan Anda menggunakan foreign key 'menu_makanan_id' di tabel Verifikasi 
        // jika Anda tidak menggunakan polymorphic. Jika menggunakan 'objek_id' dan 'tipe_objek', 
        // Anda harus memastikan nama relasi sudah sesuai.
        
        // Menggunakan asumsi dari file yang Anda berikan di prompt:
        return $this->hasOne(Verifikasi::class, 'objek_id')
                    ->where('tipe_objek', 'menu');
    }

    /**
     * Hubungan Menu Makanan ke Notifikasi (One to Many).
     * Digunakan untuk notifikasi yang terkait langsung dengan menu ini.
     */
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'menu_id');
    }


    // ===============================
    // 🧠 FUNGSI TAMBAHAN (ACCESSORS)
    // ===============================

    /**
     * Mendapatkan rata-rata rating dari semua ulasan (Virtual Attribute).
     * Akses via $menu->average_rating
     */
    protected function averageRating(): Attribute
    {
        return Attribute::get(fn () => $this->ulasan()->avg('rating') ?? 0);
    }
    
    /**
     * Memformat harga menjadi format Rupiah (Virtual Attribute).
     * Akses via $menu->formatted_harga
     */
    protected function formattedHarga(): Attribute
    {
        return Attribute::get(fn () => 'Rp ' . number_format($this->harga, 0, ',', '.'));
    }
}