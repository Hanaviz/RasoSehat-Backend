<?php

namespace App\Http\Controllers;

use App\Models\Restoran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminVerifikasiRestoranController extends Controller
{
    /**
     * Menampilkan daftar semua restoran yang statusnya PENDING untuk admin.
     * GET /api/v1/admin/restorans/pending
     */
    public function index(Request $request)
    {
        // 💡 Logika ini memastikan hanya toko 'pending' dan 'ditolak' yang mudah dilihat admin
        $query = Restoran::with('user')
                        ->where('status_verifikasi', 'pending');
        
        // Admin Dashboard Anda mungkin ingin filter status lain juga
        if ($request->query('status') === 'ditolak') {
            $query->orWhere('status_verifikasi', 'ditolak');
        }
        
        $restorans = $query->orderBy('created_at', 'asc')->get();

        // Admin perlu melihat semua data, termasuk dokumen verifikasi
        return response()->json($restorans);
    }
    
    /**
     * Menampilkan detail restoran tertentu (termasuk dokumen verifikasi).
     * GET /api/v1/admin/restorans/{id}
     */
    public function show($id)
    {
        // Admin perlu melihat relasi menu yang sedang pending juga
        $restoran = Restoran::with(['user', 'menus' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])->findOrFail($id); 
        
        // Mengembalikan semua data, termasuk path dokumen
        return response()->json($restoran);
    }

    /**
     * Mengubah status verifikasi restoran.
     * POST /api/v1/admin/restorans/{id}/status
     * Menerima: status_verifikasi ('disetujui' atau 'ditolak'), catatan (opsional)
     */
    public function updateStatus(Request $request, $id)
    {
        // Periksa apakah user yang mengakses benar-benar Admin (sudah di-handle di routes/api.php)
        $request->validate([
            'status_verifikasi' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string'
        ]);

        $restoran = Restoran::with('user')->findOrFail($id);
        $newStatus = $request->status_verifikasi;
        
        // 1. Update Status dan Catatan Admin
        $restoran->status_verifikasi = $newStatus;
        // 💡 Memastikan field 'catatan_admin' digunakan dan disimpan
        $restoran->catatan_admin = $request->catatan; 
        $restoran->save();

        // 2. LOGIKA OTOMATISASI KRITIS RasoSehat: Role dan Aktivasi Menu
        if ($newStatus === 'disetujui') {
            // Mengubah role user menjadi 'penjual' jika belum
            if ($restoran->user && $restoran->user->role !== 'penjual') {
                $restoran->user->role = 'penjual';
                $restoran->user->save();
            }
            // Mengaktifkan SEMUA menu yang mungkin sudah diupload sebelumnya oleh penjual
            // Karena toko disetujui, menu-menu ini juga dianggap terverifikasi
            $restoran->menus()->update(['status_verifikasi' => 'disetujui']);
        }
        
        // 3. Kirim Notifikasi (Log atau Email)
        // Log::info("Admin (User ID: {$request->user()->id}) mengubah status Restoran {$restoran->nama_restoran} menjadi {$newStatus}.");

        // Mail::to($restoran->user->email)->send(new RestoranStatusMail($restoran, $request->catatan));

        return response()->json([
            'message' => "Status verifikasi restoran berhasil diperbarui menjadi {$newStatus}.",
            'data' => $restoran
        ]);
    }
}