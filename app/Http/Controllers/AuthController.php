<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse; // Untuk tipe hinting pada redirect

// PARENT CONTROLLER: Harus mewarisi dari Controller utama
class AuthController extends Controller
{
    // ----------------------------------------------------------------------
    // 1. WEB VIEW HANDLERS (Menampilkan Form Blade)
    // ----------------------------------------------------------------------

    /**
     * Tampilkan halaman Sign In (Login Form) Blade.
     * GET /signin
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        // Asumsi file Blade ada di resources/views/pages/auth/signin.blade.php
        return view('pages.auth.signin');
    }

    /**
     * Tampilkan halaman Sign Up (Registration Form) Blade.
     * GET /signup
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        // Asumsi file Blade ada di resources/views/pages/auth/signup.blade.php
        return view('pages.auth.signup');
    }
    
    // ----------------------------------------------------------------------
    // 2. WEB AUTH LOGIC (Menggunakan Session)
    // ----------------------------------------------------------------------

    /**
     * Tangani proses login pengguna (menggunakan Session Guard).
     * POST /signin
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            // Mengganti 'email' dengan 'nomorHP' jika menggunakan nomor HP di form signin.jsx
            'email' => ['required', 'email'], 
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // Regenerate session untuk menghindari serangan session fixation
            $request->session()->regenerate();

            // Redirect ke halaman utama (Herosection)
            return redirect()->intended(route('home'));
        }

        // Jika gagal, redirect kembali dengan pesan error
        return back()->withErrors([
            'email' => 'Kredensial tidak valid. Periksa email atau password Anda.',
        ])->onlyInput('email');
    }

    /**
     * Tangani proses pendaftaran pengguna (menggunakan Session Guard).
     * POST /signup
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            // Sesuaikan dengan field di SignUp.jsx: nama, nomorHP/email, kataSandi
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6', // Di React ada validasi min 6
            // Tambahkan validasi tanggal lahir, jenis kelamin, phone jika ada di form blade Anda
            'tanggalLahir' => 'nullable|date',
            'jenisKelamin' => 'nullable|in:Laki-laki,Perempuan',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pembeli',
            // Tambahkan kolom lain seperti phone/tanggal_lahir setelah dimodifikasi di form Blade
        ]);
        
        // Login otomatis setelah pendaftaran berhasil
        Auth::login($user); 

        // Redirect ke home (Herosection) setelah berhasil
        return redirect()->route('home');
    }

    /**
     * Tangani proses logout pengguna (Sesi Web).
     * POST /logout
     */
    public function logout(Request $request): RedirectResponse
    {
        // Logout dari guard 'web'
        Auth::guard('web')->logout();

        // Invalidate session dan regenerate token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman login/home
        return redirect()->route('login')->with('status', 'Anda berhasil keluar.');
    }
    
    // ----------------------------------------------------------------------
    // 3. API ENDPOINTS (Diberi suffix 'Api' untuk membedakan dari Web)
    // ----------------------------------------------------------------------
    
    /**
     * [API] Pendaftaran Pengguna Baru (Pembeli default).
     * POST /api/v1/register
     */
    public function registerApi(Request $request)
    {
        try {
            // Logic validasi dan pembuatan user sama
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pembeli',
        ]);
        
        // Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Pendaftaran berhasil. Selamat datang di RasoSehat!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }
    
    /**
     * [API] Endpoint Login Pengguna.
     * POST /api/v1/login
     */
    public function loginApi(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Kredensial tidak valid. Periksa email atau password Anda.'], 401);
        }

        $user = Auth::user();
        
        // Hapus token lama dan buat token baru (API Logic)
        $user->tokens()->delete();
        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * [API] Endpoint Logout Pengguna.
     * POST /api/v1/logout
     */
    public function logoutApi(Request $request)
    {
        // Hapus token Sanctum
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout berhasil. Token telah dicabut.']);
    }

    /**
     * [API] Mendapatkan data user yang sedang login.
     * GET /api/v1/user
     */
    public function userApi(Request $request)
    {
        return response()->json($request->user());
    }
}