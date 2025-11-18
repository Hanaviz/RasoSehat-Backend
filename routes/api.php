<?php

use Illuminate\Support\Facades\Route;
// Import semua Controller yang diperlukan
use App\Http\Controllers\UserController;
use App\Http\Controllers\PublicMenuController;     // Menu publik (terverifikasi)
use App\Http\Controllers\MerchantMenuController;   // Management menu oleh penjual
use App\Http\Controllers\RestoranController;       // Detail & Update Restoran
use App\Http\Controllers\UlasanController;         // Ulasan menu
use App\Http\Controllers\KategoriMakananController; // Kategori
use App\Http\Controllers\AdminVerifikasiRestoranController; // Admin Toko
use App\Http\Controllers\VerifikasiMenuController;      // Admin Menu
use App\Http\Controllers\NotifikasiController;

// Rute-rute ini otomatis memiliki prefix /api dari konfigurasi Laravel

// =========================================================================
// 1. PUBLIC ROUTES (/api/v1/...)
// Data yang ditampilkan ke publik dan sudah diverifikasi. Tidak memerlukan login.
// =========================================================================
Route::group(['prefix' => 'v1'], function () {
    
    // --- Autentikasi Publik (Register & Login) ---
    // Pastikan Anda memiliki Controller Auth yang sebenarnya untuk ini
    // Route::post('/register', [UserController::class, 'register']); 
    // Route::post('/login', [UserController::class, 'login']); 
    
    // --- Data Menu & Pencarian ---
    // Menggunakan PublicMenuController untuk memastikan filter status 'disetujui'
    Route::get('/menus', [PublicMenuController::class, 'index']);      // GET /api/v1/menus (Daftar semua menu terverifikasi)
    Route::get('/menus/{id}', [PublicMenuController::class, 'show']);   // GET /api/v1/menus/{id}
    Route::get('/search', [PublicMenuController::class, 'search']);     // GET /api/v1/search?q=query&category_id=X (Pencarian/Filter)
    
    // --- Data Pendukung Publik ---
    Route::get('/kategoris', [KategoriMakananController::class, 'index']); // List kategori makanan
    Route::get('/restorans/{id}', [RestoranController::class, 'show']);     // Detail Restoran
});


// =========================================================================
// 2. PROTECTED ROUTES (/api/v1/...)
// Membutuhkan token Sanctum via middleware 'auth:sanctum'.
// =========================================================================
Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    
    // Route::post('/logout', [UserController::class, 'logout']); 
    
    // --- 2.1 USER/PEMBELI ROUTES ---
    Route::post('/ulasan', [UlasanController::class, 'store']); // Buat ulasan baru
    // Route::get('/user/profile', [UserController::class, 'profile']); 
    
    
    // --- 2.2 MERCHANT/PENJUAL ROUTES (Membutuhkan role:penjual) ---
    Route::group(['middleware' => ['role:penjual']], function () {
        
        // Pendaftaran Toko (HANYA untuk pertama kali)
        Route::post('/restoran/register', [RestoranController::class, 'store']); // POST /api/v1/restoran/register (RegisterStorePage)
        
        // Management Menu (Menggunakan MerchantMenuController yang terlindungi)
        Route::get('/merchant/menus', [MerchantMenuController::class, 'index']);  // GET /api/v1/merchant/menus (Daftar menu milik sendiri)
        Route::post('/merchant/menus', [MerchantMenuController::class, 'store']);  // POST /api/v1/merchant/menus (AddMenuPage)
        Route::put('/merchant/menus/{id}', [MerchantMenuController::class, 'update']);
        Route::delete('/merchant/menus/{id}', [MerchantMenuController::class, 'destroy']);
        
        // Update Profil Toko oleh Penjual
        Route::put('/restoran/{id}', [RestoranController::class, 'update']);
    });
    
    
    // --- 2.3 ADMIN ROUTES (Membutuhkan role:admin) ---
    Route::group(['middleware' => ['role:admin']], function () {
        
        // Verifikasi Restoran
        Route::get('/admin/restorans/pending', [AdminVerifikasiRestoranController::class, 'index']);     // List pending
        Route::post('/admin/restorans/{id}/status', [AdminVerifikasiRestoranController::class, 'updateStatus']); // Set status
        
        // Verifikasi Menu
        Route::get('/admin/menus/pending', [VerifikasiMenuController::class, 'index']); // List pending menu untuk diaudit
        Route::post('/admin/menus/{id}/status', [VerifikasiMenuController::class, 'updateStatus']);      // Set status (disetujui/ditolak)
        
        // Notifikasi
        Route::get('/notifikasi/admin', [NotifikasiController::class, 'index']);
        Route::post('/notifikasi/read/{id}', [NotifikasiController::class, 'markAsRead']);
    });

});