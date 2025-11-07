<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role', // admin | penjual | pembeli
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ==========================
    // 🔗 RELASI MODEL
    // ==========================

    /**
     * Satu user bisa memiliki banyak restoran.
     * Contoh: user dengan role 'penjual'.
     */
    public function restorans()
    {
        return $this->hasMany(Restoran::class, 'user_id');
    }

    /**
     * Satu user bisa memberikan banyak ulasan.
     * Contoh: user dengan role 'pembeli'.
     */
    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'user_id');
    }

    /**
     * Satu user (admin) bisa melakukan banyak verifikasi.
     * Contoh: user dengan role 'admin'.
     */
    public function verifikasis()
    {
        return $this->hasMany(Verifikasi::class, 'admin_id');
    }

    /**
     * Relasi ke tabel notifikasi (opsional jika kamu punya tabel notifikasi manual).
     * Biasanya Laravel sudah punya sistem bawaan untuk notifikasi email.
     */
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPenjual()
    {
        return $this->role === 'penjual';
    }

    public function isPembeli()
    {
        return $this->role === 'pembeli';
    }
}

