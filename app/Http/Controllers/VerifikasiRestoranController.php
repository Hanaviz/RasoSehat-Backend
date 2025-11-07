<?php

namespace App\Http\Controllers;

use App\Models\Restoran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VerifikasiRestoranController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:menunggu,disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        $restoran = Restoran::findOrFail($id);
        $restoran->status_verifikasi = $request->status_verifikasi;
        $restoran->save();

        // catat log
        VerifikasiController::buatLog('restoran', $id, $request->status_verifikasi, $request->catatan);

        // kirim email notifikasi ke pemilik restoran
        $user = $restoran->user; // pastikan relasi user() ada di model Restoran
        if ($user && $user->email) {
            Mail::raw(
                "Halo {$user->name}, status verifikasi restoran Anda telah berubah menjadi: {$request->status_verifikasi}. Catatan: {$request->catatan}",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Perubahan Status Verifikasi Restoran Anda');
                }
            );
        }

        return response()->json([
            'message' => 'Status verifikasi restoran diperbarui dan notifikasi dikirim.',
            'data' => $restoran
        ]);
    }
}