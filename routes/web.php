<?php

use Illuminate\Support\Facades\Route;

// Import semua Controller yang diperlukan untuk melayani View
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\RestoranController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminVerifikasiController; // Untuk Admin Dashboard
use App\Http\Controllers\VerifikasiMenuController;
use App\Http\Controllers\AdminVerifikasiRestoranController;


// =========================================================================
// 1. PUBLIC & AUTHENTICATION ROUTES
// =========================================================================

// Halaman Utama (Herosection)
Route::get('/', [PublicMenuController::class, 'index'])->name('home');

// Halaman Pencarian & Detail Publik
Route::get('/search', [PublicMenuController::class, 'search'])->name('search'); // SearchResultsPage
Route::get('/category/{categorySlug}', [PublicMenuController::class, 'showCategory'])->name('category.show'); // CategoryPage
Route::get('/menu/{slug}', [PublicMenuController::class, 'showMenuDetail'])->name('menu.detail'); // MenuDetailPage
Route::get('/restaurant/{slug}', [RestoranController::class, 'showRestoranDetail'])->name('restaurant.detail'); // RestaurantDetailPage


// AUTHENTICATION (Menggunakan metode Web dari AuthController yang diperbaiki)
Route::group(['middleware' => 'guest'], function () {
    Route::get('/signin', [AuthController::class, 'showLoginForm'])->name('login'); // Signin.jsx
    Route::post('/signin', [AuthController::class, 'login']); 

    Route::get('/signup', [AuthController::class, 'showRegistrationForm'])->name('register'); // SignUp.jsx
    Route::post('/signup', [AuthController::class, 'register']);
});


// =========================================================================
// 2. PROTECTED USER ROUTES (Membutuhkan Login)
// =========================================================================

Route::middleware('auth')->group(function () {
    // Aksi Logout (digunakan oleh NavbarAuth)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); 
    
    // Halaman Profil & Pengaturan (Digunakan oleh NavbarAuth)
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile'); // Profile.jsx
    Route::get('/settings', [UserController::class, 'showSettings'])->name('settings'); // SettingsPage.jsx

    // Rute Notifikasi (Opsional)
    Route::get('/notifications', [NotifikasiController::class, 'index'])->name('notifications');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
});


// =========================================================================
// 3. MERCHANT ROUTES (Membutuhkan Role Penjual)
// Asumsi Anda menggunakan Middleware atau Gate di Controller
// =========================================================================

Route::middleware(['auth', 'role:penjual'])->group(function () {
    Route::get('/register-store', [RestoranController::class, 'showRegistrationForm'])->name('register-store'); // RegisterStorePage
    Route::post('/restoran/register', [RestoranController::class, 'store'])->name('restoran.store'); // Handle form pendaftaran toko

    Route::get('/my-store', [RestoranController::class, 'showDashboard'])->name('my-store'); // MyStorePage
    Route::get('/add-menu', [MerchantMenuController::class, 'create'])->name('add-menu'); // AddMenuPage
    Route::post('/merchant/menus', [MerchantMenuController::class, 'store'])->name('merchant.menu.store'); // Simpan menu baru
});


// =========================================================================
// 4. ADMIN ROUTES (Membutuhkan Role Admin)
// =========================================================================

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin-dashboard', [AdminVerifikasiController::class, 'showDashboard'])->name('admin-dashboard'); // AdminDashboardPage
    
    // Rute Verifikasi
    Route::get('/admin/restorans/pending', [AdminVerifikasiRestoranController::class, 'index'])->name('admin.restoran.pending');
    Route::post('/admin/restorans/{id}/status', [AdminVerifikasiRestoranController::class, 'updateStatus'])->name('admin.restoran.updateStatus');
    
    // Alias dari /admin ke /admin-dashboard
    Route::get('/admin', function () {
        return redirect()->route('admin-dashboard');
    });
});