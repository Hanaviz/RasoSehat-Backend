<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Restoran;

class RestoranController extends Controller
{
    // 🔹 GET /api/restorans
    public function index()
    {
        return response()->json(Restoran::all(), 200);
    }

    // 🔹 POST /api/restorans
    public function store(Request $request)
    {
        // Validasi umum
        $rules = [
            'user_id' => 'required|exists:users,id',
            'nama_restoran' => 'required|string|max:100',
            'nama_pemilik' => 'required|string|max:100',
            'kategori_toko' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'alamat' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'no_telepon' => 'nullable|string|max:20',
            'no_whatsapp' => 'required|string|max:20',
            'jam_operasional' => 'required|string|max:100',
            'media_sosial' => 'nullable|string',
            'jenis_usaha' => 'required|in:perorangan,korporasi',
            'verifikasi_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ];

        // Validasi tambahan jika korporasi
        if ($request->jenis_usaha === 'korporasi') {
            $rules['verifikasi_npwp'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:4096';
            $rules['verifikasi_nib_siup'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:4096';
            $rules['verifikasi_akta_pendirian'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:4096';
        }

        $validated = $request->validate($rules);

        // Upload file
        $ktpPath = $request->file('verifikasi_ktp')->store('uploads/verifikasi/ktp', 'public');
        $npwpPath = $request->hasFile('verifikasi_npwp') ? $request->file('verifikasi_npwp')->store('uploads/verifikasi/npwp', 'public') : null;
        $nibPath = $request->hasFile('verifikasi_nib_siup') ? $request->file('verifikasi_nib_siup')->store('uploads/verifikasi/nib_siup', 'public') : null;
        $aktaPath = $request->hasFile('verifikasi_akta_pendirian') ? $request->file('verifikasi_akta_pendirian')->store('uploads/verifikasi/akta', 'public') : null;

        // Simpan ke database
        $restoran = Restoran::create([
            'user_id' => $validated['user_id'],
            'nama_restoran' => $validated['nama_restoran'],
            'nama_pemilik' => $validated['nama_pemilik'],
            'kategori_toko' => $validated['kategori_toko'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'alamat' => $validated['alamat'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'no_telepon' => $validated['no_telepon'] ?? null,
            'no_whatsapp' => $validated['no_whatsapp'],
            'jam_operasional' => $validated['jam_operasional'],
            'media_sosial' => $validated['media_sosial'] ?? null,
            'jenis_usaha' => $validated['jenis_usaha'],
            'status_verifikasi' => 'pending',
            'verifikasi_ktp' => $ktpPath,
            'verifikasi_npwp' => $npwpPath,
            'verifikasi_nib_siup' => $nibPath,
            'verifikasi_akta_pendirian' => $aktaPath,
        ]);

        return response()->json([
            'message' => 'Pendaftaran restoran berhasil, menunggu verifikasi admin.',
            'data' => $restoran
        ], 201);
    }

    // 🔹 GET /api/restorans/{id}
    public function show($id)
    {
        $restoran = Restoran::find($id);
        if (!$restoran) {
            return response()->json(['message' => 'Restoran tidak ditemukan'], 404);
        }
        return response()->json($restoran, 200);
    }

    // 🔹 PUT/PATCH /api/restorans/{id}
    public function update(Request $request, $id)
    {
        $restoran = Restoran::find($id);
        if (!$restoran) {
            return response()->json(['message' => 'Restoran tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_restoran' => 'nullable|string|max:100',
            'nama_pemilik' => 'nullable|string|max:100',
            'kategori_toko' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'no_telepon' => 'nullable|string|max:20',
            'no_whatsapp' => 'nullable|string|max:20',
            'jam_operasional' => 'nullable|string|max:100',
            'media_sosial' => 'nullable|string',
            'jenis_usaha' => 'nullable|in:perorangan,korporasi',
            'status_verifikasi' => 'nullable|string|in:pending,disetujui,ditolak',
            'verifikasi_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'verifikasi_npwp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'verifikasi_nib_siup' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'verifikasi_akta_pendirian' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // Handle upload file baru
        foreach (['verifikasi_ktp', 'verifikasi_npwp', 'verifikasi_nib_siup', 'verifikasi_akta_pendirian'] as $field) {
            if ($request->hasFile($field)) {
                if ($restoran->$field) Storage::disk('public')->delete($restoran->$field);
                $restoran->$field = $request->file($field)->store('uploads/verifikasi/' . $field, 'public');
            }
        }

        $restoran->update($validated);

        return response()->json([
            'message' => 'Data restoran berhasil diperbarui.',
            'data' => $restoran
        ]);
    }

    // 🔹 DELETE /api/restorans/{id}
    public function destroy($id)
    {
        $restoran = Restoran::find($id);
        if (!$restoran) {
            return response()->json(['message' => 'Restoran tidak ditemukan'], 404);
        }

        // Hapus semua file yang tersimpan
        foreach (['verifikasi_ktp', 'verifikasi_npwp', 'verifikasi_nib_siup', 'verifikasi_akta_pendirian'] as $field) {
            if ($restoran->$field) Storage::disk('public')->delete($restoran->$field);
        }

        $restoran->delete();

        return response()->json(['message' => 'Restoran berhasil dihapus'], 200);
    }
}
