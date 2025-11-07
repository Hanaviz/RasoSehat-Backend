<?php

namespace App\Http\Controllers;

use App\Models\Restoran;
use App\Mail\RestoranStatusMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminVerifikasiRestoranController extends Controller
{
    /**
     * 🧾 Menampilkan daftar semua restoran untuk admin (opsional filter).
     */
    public function index(Request $request)
    {
        $status = $request->query('status'); // contoh: ?status=pending
        $query = Restoran::with('user');

        if ($status) {
            $query->where('status_verifikasi', $status);
        }

        $restorans = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $restorans
        ]);
    }

    /**
     * 🔍 Menampilkan detail restoran tertentu.
     */
    public function show($id)
    {
        $restoran = Restoran::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $restoran
        ]);
    }

    /**
     * ✏️ Mengubah status verifikasi restoran.
     * - status_verifikasi: 'pending', 'disetujui', 'ditolak'
     * - pesan_tambahan: opsional untuk ditampilkan di email
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:pending,disetujui,ditolak',
            'pesan_tambahan' => 'nullable|string'
        ]);

        $restoran = Restoran::with('user')->findOrFail($id);

        $restoran->status_verifikasi = $request->status_verifikasi;
        $restoran->save();

        // Kirim email notifikasi ke pemilik restoran
        if ($restoran->user && $restoran->user->email) {
            Mail::to($restoran->user->email)
                ->send(new RestoranStatusMail($restoran, $request->pesan_tambahan));
        }

        return response()->json([
            'success' => true,
            'message' => "Status verifikasi restoran berhasil diperbarui menjadi {$restoran->status_verifikasi}",
            'data' => $restoran
        ]);
    }
}
