<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'tipe_objek',     // restoran | menu
        'objek_id',
        'status',         // pending | disetujui | ditolak
        'catatan',
        'tanggal_verifikasi',
    ];

    // 🔗 Relasi ke admin (user yang melakukan verifikasi)
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // 🔗 Relasi ke restoran (jika tipe_objek = restoran)
    public function restoran()
    {
        return $this->belongsTo(Restoran::class, 'objek_id')
                    ->where('tipe_objek', 'restoran');
    }

    // 🔗 Relasi ke menu (jika tipe_objek = menu)
    public function menu()
    {
        return $this->belongsTo(MenuMakanan::class, 'objek_id')
                    ->where('tipe_objek', 'menu');
    }
}
