<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// PASTIKAN CONTROLLER INI MEWARISI METHOD getNavbarData() DARI CONTROLLER UTAMA
class UserController extends Controller
{
    /**
     * Tampilkan halaman Profile untuk user yang sedang login (Web/Blade).
     * Sesuai dengan Profile.jsx yang menampilkan data diri user terautentikasi.
     */
    public function showProfile()
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();
        
        if (!$user) {
            // Jika user tidak terautentikasi, redirect ke halaman login
            return redirect()->route('login');
        }
        
        // Mengambil data navbar (user data dan notifikasi)
        $navbarData = $this->getNavbarData();
        
        // Menggabungkan data spesifik halaman (data user) dengan data navbar
        return view('pages.user.profile', array_merge($navbarData, [
            // Data user yang akan ditampilkan di form/UI halaman profile
            'user' => $user, 
            'userData' => $navbarData['userData'], // Data user yang sudah diformat dari navbar helper
        ]));
    }

    // ----------------------------------------------------------------------
    // METODE API LAMA (DIPERTAHANKAN UNTUK KEPENTINGAN API / AJAX)
    // ----------------------------------------------------------------------

    /**
     * Tampilkan semua data user (API Endpoint).
     */
    public function indexApi()
    {
        // Method ini tetap sebagai API endpoint untuk mengambil daftar semua user
        $users = User::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar semua pengguna (API)',
            'data' => $users
        ], 200);
    }

    /**
     * Simpan user baru ke database (API Endpoint, mungkin untuk pendaftaran).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'phone'     => 'nullable|string|max:20',
            'role'      => 'nullable|in:admin,penjual,pembeli'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    /**
     * Tampilkan detail user berdasarkan ID (API Endpoint).
     */
    public function showApi($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    /**
     * Update data user (API Endpoint, bisa digunakan form AJAX di Profile).
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'name'      => 'sometimes|string|max:100',
            'email'     => 'sometimes|email|unique:users,email,' . $id,
            'password'  => 'sometimes|string|min:6',
            'phone'     => 'nullable|string|max:20',
            'role'      => 'nullable|in:admin,penjual,pembeli'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        
        // Jika digunakan untuk form update di Blade, Anda mungkin ingin me-redirect:
        // return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');

        // Karena ini API/AJAX, kembalikan JSON
        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui',
            'data' => $user
        ], 200);
    }

    /**
     * Hapus user berdasarkan ID (API Endpoint).
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ], 200);
    }
}