<?php

namespace App\Http\Controllers;

use App\Models\Verifikasi;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    /**
     * Buat log verifikasi baru (dipanggil oleh controller lain)
     */
    public static function buatLog($tipe_objek, $objek_id, $status, $catatan = null)
    {
        return Verifikasi::create([
            'tipe_objek' => $tipe_objek,
            'objek_id' => $objek_id,
            'status' => $status,
            'catatan' => $catatan,
            'tanggal_verifikasi' => now(),
        ]);
    }

    /**
     * Tampilkan semua log verifikasi
     */
    public function index()
    {
        return response()->json(Verifikasi::latest()->get());
    }
}
