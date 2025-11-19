<?php

namespace App\Http\Controllers;

use App\Models\MenuMakanan;
use App\Models\Restoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log; // Import Log

class VerifikasiMenuController extends Controller
{
    /**
     * Menampilkan daftar semua menu yang statusnya PENDING untuk audit Admin.
     */
    public function index()
    {
        // Ambil semua menu yang status verifikasinya 'pending'
        $pendingMenus = MenuMakanan::with(['restoran', 'kategori'])
                                    ->where('status_verifikasi', 'pending')
                                    ->orderBy('created_at', 'asc')
                                    ->get();
        
        return response()->json($pendingMenus);
    }
    
    /**
     * Mengubah status verifikasi menu.
     * Menerima: status_verifikasi (disetujui/ditolak), catatan (opsional)
     */
    public function updateStatus(Request $request, $id)
    {
        // Pastikan hanya Admin yang bisa melakukan ini (sudah di-handle di routes/api.php)
        $request->validate([
            'status_verifikasi' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string', // Catatan penting untuk feedback jika ditolak
        ]);

        $menu = MenuMakanan::with('restoran.user')->findOrFail($id);
        
        // 1. UPDATE STATUS DAN CATATAN
        $menu->status_verifikasi = $request->status_verifikasi;
        $menu->catatan_admin = $request->catatan;
        $menu->save();

        // 2. KIRIM NOTIFIKASI KE PENJUAL (Sederhana)
        $user = $menu->restoran->user ?? null;
        if ($user && $user->email) {
            $subject = $menu->status_verifikasi === 'disetujui' 
                ? "🎉 Menu Anda '{$menu->nama_menu}' Telah Disetujui!"
                : "⚠️ Menu Anda '{$menu->nama_menu}' Ditolak - Perlu Revisi";
            
            $body = "Status verifikasi menu Anda di RasoSehat telah diubah menjadi: {$menu->status_verifikasi}.\n\n";
            
            if ($request->catatan) {
                $body .= "Catatan Admin: {$request->catatan}\n\n";
            }
            
            // Simulasikan pengiriman email (Ganti dengan Mail::to jika mail Anda sudah dikonfigurasi)
            Log::info("Notifikasi Penjual ({$user->email}): {$subject}");
            
            // Mail::to($user->email)->send(new MenuStatusMail($menu, $request->catatan));
        }

        return response()->json([
            'message' => "Status menu '{$menu->nama_menu}' berhasil diperbarui menjadi {$menu->status_verifikasi}.",
            'data' => $menu
        ]);
    }
}