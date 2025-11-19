<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Endpoint Pendaftaran Pengguna Baru (Pembeli default).
     * POST /api/v1/register
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                // Anda bisa menambahkan 'tanggal_lahir', 'jenis_kelamin', 'phone' di sini
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pembeli', // Default role
            // Tambahkan kolom lain di sini
        ]);
        
        // Autologin setelah register dan buat token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Pendaftaran berhasil. Selamat datang di RasoSehat!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    /**
     * Endpoint Login Pengguna.
     * POST /api/v1/login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Kredensial tidak valid. Periksa email atau password Anda.'
            ], 401);
        }

        $user = Auth::user();
        
        // Hapus token lama untuk keamanan
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * Endpoint Logout Pengguna.
     * POST /api/v1/logout
     */
    public function logout(Request $request)
    {
        // Hapus semua token yang terkait dengan pengguna saat ini
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil. Token telah dicabut.'
        ]);
    }

    /**
     * Mendapatkan data user yang sedang login (untuk pengecekan Auth di Frontend).
     * GET /api/v1/user
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}