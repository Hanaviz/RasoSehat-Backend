<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id',
        'restoran_id',
        'menu_id',
        'judul',
        'pesan',
        'status',
    ];

    // 🔗 Relasi ke user penerima notifikasi
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔗 Relasi opsional ke restoran (jika notifikasi terkait restoran)
    public function restoran()
    {
        return $this->belongsTo(Restoran::class, 'restoran_id');
    }

    // 🔗 Relasi opsional ke menu (jika notifikasi terkait menu)
    public function menu()
    {
        return $this->belongsTo(MenuMakanan::class, 'menu_id');
    }
}