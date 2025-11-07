<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    // Menampilkan semua notifikasi milik user login
    public function index()
    {
        $notifikasis = Notifikasi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notifikasi.index', compact('notifikasis'));
    }

    // Tandai notifikasi sebagai dibaca
    public function markAsRead($id)
    {
        $notifikasi = Notifikasi::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($notifikasi) {
            $notifikasi->update(['status' => 'dibaca']);
        }

        return redirect()->back();
    }
}
