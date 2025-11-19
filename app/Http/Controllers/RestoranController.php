<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Restoran;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Diperlukan untuk otorisasi

class RestoranController extends Controller
{
    // =========================================================
    // 🔹 STORE: Mendaftarkan Toko (POST /api/v1/restoran/register)
    // Dijalankan oleh Penjual yang sudah login (user_id diambil dari Auth)
    // =========================================================
    public function store(Request $request)
    {
        // 🚨 Wajib: Ambil user yang sedang login
        $user = $request->user();

        // Cek duplikasi: Pastikan user belum memiliki restoran
        if ($user->restoran) {
            return response()->json([
                'message' => 'Anda sudah memiliki restoran terdaftar di RasoSehat.'
            ], 403); // Forbidden
        }
        
        // 1. Definisikan Aturan Validasi
        $rules = [
            'nama_restoran' => 'required|string|max:100|unique:restorans,nama_restoran',
            'nama_pemilik' => 'required|string|max:100',
            'kategori_toko' => 'required|string|max:100', // Dari SelectField RegisterStorePage
            'deskripsi' => 'nullable|string',
            'alamat' => 'required|string',
            'latitude' => 'nullable|numeric', // Dari Map Picker
            'longitude' => 'nullable|numeric', // Dari Map Picker
            'no_telepon' => 'nullable|string|max:20',
            'no_whatsapp' => 'required|string|max:20',
            'jam_operasional' => 'required|string|max:100',
            'media_sosial' => 'nullable|string',
            'jenis_usaha' => 'required|in:perorangan,korporasi',
            
            // Verifikasi Dokumen
            'verifikasi_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096', // 4MB
            // Asumsi: File Korporasi dikirim dalam field yang sama, tetapi nullable
            'verifikasi_npwp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'verifikasi_nib_siup' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'verifikasi_akta_pendirian' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',

            // NEW FIELDS FROM REGISTER STORE PAGE
            'health_focus' => 'required|string', // Kunci fokus kesehatan
            'dominant_fat' => 'required|string', // Jenis minyak dominan
            'dominant_cooking_method' => 'required|string', // Metode masak
            'sales_channels' => 'required|string', // Saluran penjualan
        ];

        // Tambahan Validasi jika Korporasi (Wajibkan dokumen korporasi)
        if ($request->jenis_usaha === 'korporasi') {
             $rules['verifikasi_npwp'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:4096';
             // ... Tambahkan aturan required untuk NIB dan Akta di sini
        }

        $validated = $request->validate($rules);

        // 2. Proses Upload File
        $data = $validated;
        $fileFields = [
            'verifikasi_ktp', 'verifikasi_npwp', 'verifikasi_nib_siup', 'verifikasi_akta_pendirian'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Simpan file di folder terpisah sesuai nama field
                $path = $request->file($field)->store("uploads/verifikasi/{$field}", 'public');
                $data[$field] = $path;
            } else {
                 $data[$field] = null;
            }
        }
        
        // 3. Simpan ke database
        $restoran = Restoran::create(array_merge($data, [
            'user_id' => $user->id, // 🚨 CRITICAL FIX: Ambil user_id dari Auth
            'status_verifikasi' => 'pending', // Default
        ]));
        
        // 4. Update Role Pengguna menjadi Penjual (Jika belum)
        if ($user->role !== 'penjual') {
            $user->role = 'penjual';
            $user->save();
        }

        return response()->json([
            'message' => 'Pendaftaran restoran berhasil, menunggu verifikasi admin.',
            'data' => $restoran
        ], 201);
    }

    // =========================================================
    // 🔹 SHOW: Detail Restoran (GET /api/v1/restorans/{id})
    // =========================================================
    public function show($id)
    {
        // 💡 Perluas dengan relasi yang penting (misal: menus)
        $restoran = Restoran::with(['user', 'menus' => function ($query) {
            // Hanya tampilkan menu yang sudah disetujui (VERIFIED)
            $query->where('status_verifikasi', 'disetujui');
        }])->findOrFail($id); 

        // CRITICAL: Filter data verifikasi (KTP, NPWP, dll.) agar tidak tampil ke publik
        $restoran->makeHidden([
            'verifikasi_ktp', 'verifikasi_npwp', 'verifikasi_nib_siup', 'verifikasi_akta_pendirian'
        ]);

        return response()->json($restoran, 200);
    }

    // =========================================================
    // 🔹 UPDATE: Update Toko oleh Penjual (PUT /api/v1/restorans/{id})
    // =========================================================
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $restoran = Restoran::findOrFail($id);

        // 🚨 CRITICAL FIX 1: Pengecekan Kepemilikan (Otorisasi)
        if ($restoran->user_id !== $user->id) {
            // Pastikan Admin bisa bypass (Asumsi: Admin tidak menggunakan route ini)
            return response()->json(['message' => 'Akses ditolak. Anda hanya dapat memperbarui toko milik Anda.'], 403);
        }
        
        // 🚨 CRITICAL FIX 2: Mencegah Penjual Mengubah Status Verifikasi
        $validatedData = $request->except(['status_verifikasi']); // Hapus field status verifikasi dari request

        $rules = [
            'nama_restoran' => 'nullable|string|max:100|unique:restorans,nama_restoran,' . $id,
            'nama_pemilik' => 'nullable|string|max:100',
            // ... (lanjutkan rules lainnya)
        ];

        // Jika ada file yang diupload, tambahkan aturan file ke rules
        if ($request->hasFile('verifikasi_ktp')) {
            $rules['verifikasi_ktp'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096';
        }
        
        // ... lanjutkan validasi lainnya ...

        $validated = $request->validate($rules);

        // Handle upload file baru
        foreach (['verifikasi_ktp', 'verifikasi_npwp', 'verifikasi_nib_siup', 'verifikasi_akta_pendirian'] as $field) {
            if ($request->hasFile($field)) {
                // Hapus file lama jika ada
                if ($restoran->$field) Storage::disk('public')->delete($restoran->$field);
                $validated[$field] = $request->file($field)->store('uploads/verifikasi/' . $field, 'public');
            }
        }
        
        // Update data
        $restoran->update($validated);

        return response()->json([
            'message' => 'Data restoran berhasil diperbarui. Status verifikasi tetap sama.',
            'data' => $restoran
        ]);
    }

    // =========================================================
    // 🔹 DELETE: Hapus Toko oleh Penjual (DELETE /api/v1/restorans/{id})
    // =========================================================
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $restoran = Restoran::findOrFail($id);
        
        // 🚨 CRITICAL FIX: Pengecekan Kepemilikan (Otorisasi)
        if ($restoran->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak. Anda hanya dapat menghapus toko milik Anda.'], 403);
        }
        
        // Hapus semua file yang tersimpan
        foreach (['verifikasi_ktp', 'verifikasi_npwp', 'verifikasi_nib_siup', 'verifikasi_akta_pendirian'] as $field) {
            // Asumsi field ini menyimpan path yang bisa dihapus
            if ($restoran->$field) Storage::disk('public')->delete($restoran->$field);
        }

        $restoran->delete();

        return response()->json(['message' => 'Restoran berhasil dihapus'], 200);
    }
}