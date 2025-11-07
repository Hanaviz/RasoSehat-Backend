<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    RestoranController,
    MenuMakananController,
    KategoriMakananController,
    UlasanController,
    VerifikasiController,
    VerifikasiRestoranController,
    VerifikasiMenuController,
    AdminVerifikasiRestoranController,
    NotifikasiController
};

// Route default (homepage)
Route::get('/', fn() => view('welcome'));

// =============================
// 📦 API ROUTES
// Semua endpoint otomatis diawali dengan /api/
// =============================
Route::prefix('api')->group(function () {

    // ===== USERS =====
    Route::apiResource('users', UserController::class);

    // ===== RESTORAN (untuk user umum) =====
    Route::apiResource('restorans', RestoranController::class);
    Route::post('/restoran/daftar', [RestoranController::class, 'store']);

    // ===== MENU MAKANAN =====
    Route::apiResource('menu', MenuMakananController::class);

    // ===== KATEGORI MAKANAN =====
    Route::apiResource('kategori', KategoriMakananController::class);

    // ===== ULASAN =====
    Route::apiResource('ulasan', UlasanController::class);

    // ===== VERIFIKASI (umum) =====
    Route::apiResource('verifikasi', VerifikasiController::class);

    // =============================
    // 👑 ADMIN ROUTES
    // =============================
    Route::prefix('admin')->group(function () {
        // Verifikasi Restoran
        Route::get('/restorans', [AdminVerifikasiRestoranController::class, 'index']);
        Route::get('/restorans/{id}', [AdminVerifikasiRestoranController::class, 'show']);
        Route::put('/restorans/{id}/status', [AdminVerifikasiRestoranController::class, 'updateStatus']);

        // Verifikasi Menu
        Route::put('/menu/{id}/status', [VerifikasiMenuController::class, 'updateStatus']);
    });

    // ===== NOTIFIKASI =====
    Route::get('/notifikasi', [NotifikasiController::class, 'index']);
    Route::get('/notifikasi/read/{id}', [NotifikasiController::class, 'markAsRead']);
});
