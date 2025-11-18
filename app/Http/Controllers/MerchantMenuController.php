<?php

namespace App\Http\Controllers;

use App\Models\MenuMakanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Untuk upload foto

class MerchantMenuController extends Controller
{
    /**
     * Menampilkan menu-menu milik penjual yang sedang login.
     */
    public function index(Request $request) 
    {
        $restoran = $request->user()->restoran; // Asumsi relasi sudah ada
        if (!$restoran) {
             return response()->json(['message' => 'Anda belum terdaftar sebagai restoran.'], 404);
        }
        
        $menus = MenuMakanan::where('restoran_id', $restoran->id)
                            ->with('kategori')
                            ->orderBy('created_at', 'desc')
                            ->get();
                            
        return response()->json($menus);
    }
    
    /**
     * Menyimpan menu baru yang diajukan penjual (Wajib Login).
     * Menu akan otomatis berstatus 'pending'.
     */
    public function store(Request $request) {
        $user = $request->user();
        $restoran = $user->restoran;
        
        if (!$restoran || $restoran->status_verifikasi !== 'disetujui') {
            return response()->json([
                'message' => 'Anda belum bisa mengajukan menu. Toko Anda belum terverifikasi atau ditolak.'
            ], 403);
        }

        $validated = $request->validate([
            // Validasi Dasar Menu
            'kategori_id' => 'required|exists:kategori_makanan,id',
            'nama_menu' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:1000',
            
            // Validasi Data Audit Lite (Wajib)
            'bahan_baku' => 'required|string',
            'metode_masak' => 'required|string|max:100',
            
            // Validasi Data Nutrisi
            'kalori' => 'required|integer|min:0',
            'protein' => 'required|numeric|min:0',
            'gula' => 'required|numeric|min:0',
            'lemak' => 'required|numeric|min:0',
            'serat' => 'nullable|numeric|min:0',
            'lemak_jenuh' => 'nullable|numeric|min:0',

            // Klaim Diet
            'diet_claims' => 'required|array', // Harus ada minimal 1 klaim (dari frontend)
            'foto' => 'required|image|max:5120', // Maks 5MB
        ]);

        $fotoUrl = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('public/menu_photos');
            $fotoUrl = Storage::url($path);
        }

        $menu = MenuMakanan::create(array_merge($validated, [
            'restoran_id' => $restoran->id,
            'foto' => $fotoUrl,
            'status_verifikasi' => 'pending', // ⭐ Default selalu PENDING
        ]));

        // [LOGIC NOTIFIKASI ADMIN DI SINI]

        return response()->json([
            'message' => 'Menu berhasil diajukan untuk verifikasi Admin.', 
            'data' => $menu
        ], 201);
    }

    /**
     * Memperbarui menu milik penjual.
     */
    public function update(Request $request, $id) {
        $menu = MenuMakanan::where('restoran_id', $request->user()->restoran->id ?? null)
                           ->findOrFail($id);

        $validated = $request->validate([
            // ... masukkan validasi update di sini, termasuk foto opsional
            'status_verifikasi' => 'nullable|in:pending', // Hanya boleh diset ke pending
            'kategori_id' => 'sometimes|exists:kategori_makanan,id',
            'nama_menu' => 'sometimes|string|max:100',
            // ...
        ]);

        // Jika ada perubahan pada data yang memerlukan audit, set status ke PENDING lagi.
        if ($request->hasAny(['bahan_baku', 'metode_masak', 'diet_claims'])) {
             $validated['status_verifikasi'] = 'pending';
        }
        
        $menu->update($validated);
        return response()->json(['message' => 'Menu berhasil diperbarui, verifikasi mungkin diperlukan.'], 200);
    }

    /**
     * Menghapus menu milik penjual.
     */
    public function destroy(Request $request, $id) {
        $menu = MenuMakanan::where('restoran_id', $request->user()->restoran->id ?? null)
                           ->findOrFail($id);
        
        // Hapus foto jika ada
        if ($menu->foto) {
             Storage::delete(str_replace('/storage', 'public', $menu->foto));
        }

        $menu->delete();
        return response()->json(['message' => 'Menu berhasil dihapus.']);
    }
}