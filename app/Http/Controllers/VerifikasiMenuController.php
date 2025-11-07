<?php

namespace App\Http\Controllers;

use App\Models\MenuMakanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VerifikasiMenuController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:menunggu,disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        $menu = MenuMakanan::findOrFail($id);
        $menu->status_verifikasi = $request->status_verifikasi;
        $menu->save();

        // catat log
        VerifikasiController::buatLog('menu', $id, $request->status_verifikasi, $request->catatan);

        // kirim email ke pemilik restoran
        $restoran = $menu->restoran; // pastikan relasi restoran() ada di model MenuMakanan
        $user = $restoran ? $restoran->user : null;
        if ($user && $user->email) {
            Mail::raw(
                "Halo {$user->name}, status verifikasi menu '{$menu->nama_menu}' Anda telah berubah menjadi: {$request->status_verifikasi}. Catatan: {$request->catatan}",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Perubahan Status Verifikasi Menu Anda');
                }
            );
        }

        return response()->json([
            'message' => 'Status verifikasi menu diperbarui dan notifikasi dikirim.',
            'data' => $menu
        ]);
    }
}
