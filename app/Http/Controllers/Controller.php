<?php

namespace App\Http\Controllers;

// PASTIKAN INI ADA:
use App\Models\Notifikasi;
use App\Models\Restoran; // Opsional, tetapi membantu jika Anda ingin menggunakannya
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    // ... (metode default Anda)

    /**
     * Mengambil data umum (user, notifikasi) untuk Navbar yang terautentikasi.
     */
    protected function getNavbarData()
    {
        $user = auth()->user();
        
        if (!$user) {
            return ['userData' => null, 'notifications' => []];
        }

        // 1. Fetch Notifikasi (3 notifikasi terbaru yang belum dibaca)
        $notificationsCollection = Notifikasi::where('user_id', $user->id)
            ->where('is_read', 0)
            ->latest()
            ->take(3)
            ->get();
            
        // 2. Format data sesuai kebutuhan Blade Component (simulasi mock React)
        $notificationsFormatted = $notificationsCollection->map(function ($n) {
            return [
                'id' => $n->id,
                'type' => $n->tipe ?? 'info', // Asumsi ada kolom 'tipe'
                'title' => $n->judul,        // Asumsi ada kolom 'judul'
                'message' => $n->pesan,      // Asumsi ada kolom 'pesan'
                'time' => $n->created_at->diffForHumans(),
                'isRead' => (bool)$n->is_read,
                'icon' => $n->icon ?? '💡'
            ];
        })->toArray();

        // 3. Data User
        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            // Cek apakah user punya toko terdaftar (Asumsi ada relasi atau field di model User/Restoran)
            'isStoreMember' => $user->restoran()->exists() ?? false, // Asumsi User punya relasi hasOne/hasMany ke Restoran
            'avatar' => $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=16a34a&color=fff',
        ];

        return [
            'userData' => $userData,
            'notifications' => $notificationsFormatted,
        ];
    }
}