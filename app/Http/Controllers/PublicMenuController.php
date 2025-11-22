<?php

namespace App\Http\Controllers;

use App\Models\MenuMakanan;
use App\Models\KategoriMakanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// PASTIKAN CONTROLLER INI MEWARISI DARI CONTROLLER UTAMA
class PublicMenuController extends Controller
{
    // ----------------------------------------------------------------------
    // 1. WEB VIEW HANDLERS (Melayani Halaman Blade)
    // ----------------------------------------------------------------------

    /**
     * Tampilkan Halaman Utama (Herosection).
     * GET /
     */
    public function index()
    {
        // 1. Data Wajib Navbar (untuk NavbarAuth jika user login)
        $navbarData = $this->getNavbarData();
        
        // 2. Data Menu untuk Herosection.jsx [cite: hanaviz/rasosehat-frontend/RasoSehat-Frontend-8c1b67b4be62b510e73a84df0f2cbb11e9d1f737/src/pages/Herosection.jsx]
        // Filter KRITIS: Hanya menu yang statusnya 'disetujui'
        $menus = MenuMakanan::with(['restoran', 'kategori'])
            ->where('status_verifikasi', 'disetujui')
            ->orderByDesc('created_at') // Urutkan menu terbaru di depan
            ->limit(12) // Batasi untuk tampilan awal
            ->get();
            
        // 3. Data Kategori untuk tampilan Card Kategori
        $categories = KategoriMakanan::all();

        // Mengembalikan View Blade dengan semua data
        return view('pages.public.herosection', array_merge($navbarData, [
    'menus' => $menus,
    'allCategories' => $categories,  // TAMBAH INI
    'heroSlides' => [
        [
            'id' => 1,
            'image' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=1920',
            'slogan' => "Padang Penuh Rasa.",
            'subtext' => "Pilihan menu rendah kolesterol."
        ]
    ]
]));

    }

    /**
     * Tampilkan Halaman Detail Menu (MenuDetailPage).
     * GET /menu/{slug}
     */
    public function showMenuDetail($slug)
    {
        $navbarData = $this->getNavbarData();
        
        // Cari menu berdasarkan slug dan pastikan statusnya 'disetujui'
        $menu = MenuMakanan::with(['restoran', 'kategori', 'ulasan'])
            ->where('status_verifikasi', 'disetujui')
            ->where('slug', $slug)
            ->firstOrFail();
            
        // Mengembalikan View Blade
        return view('pages.public.menu-detail', array_merge($navbarData, [
            'menu' => $menu,
        ]));
    }

    /**
     * Tampilkan Halaman Hasil Pencarian (SearchResultsPage).
     * GET /search?q=query&category=id
     */
    public function search(Request $request)
    {
        $navbarData = $this->getNavbarData();
        $query = $request->input('q'); 
        $categoryId = $request->input('category_id'); 
        $minRating = $request->input('min_rating'); 

        $menus = MenuMakanan::with(['restoran', 'kategori', 'ulasan'])
            // 1. FILTER WAJIB: Hanya yang sudah disetujui
            ->where('status_verifikasi', 'disetujui')
            
            // 2. Filter Kata Kunci (Nama Menu, Deskripsi, Bahan Baku, Klaim Diet)
            ->when($query, function ($q) use ($query) {
                $q->where('nama_menu', 'like', '%' . $query . '%')
                  ->orWhere('deskripsi', 'like', '%' . $query . '%')
                  ->orWhere('bahan_baku', 'like', '%' . $query . '%')
                  // Pencarian JSON untuk diet_claims
                  ->orWhereJsonContains('diet_claims', $query);
            })
            
            // 3. Filter Kategori
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('kategori_id', $categoryId);
            })
            
            // 4. Filter Rating (Perlu relasi/join/subquery untuk menghitung rata-rata ulasan)
            // KODE INI PERLU DITEST SESUAI STRUKTUR DB ULASAN ANDA
            ->when($minRating, function ($q) use ($minRating) {
                // Contoh Sederhana: Join dan Hitung Rata-rata Ulasan
                $q->whereHas('ulasan', function($query) use ($minRating) {
                    $query->selectRaw('avg(rating) as average_rating')
                        ->groupBy('menu_makanan_id')
                        ->havingRaw('average_rating >= ?', [$minRating]);
                });
            })
            
            ->orderByDesc('kalori') 
            ->get();
            
        // Mengembalikan View Blade
        return view('pages.public.search-results', array_merge($navbarData, [
            'menus' => $menus,
            'searchQuery' => $query,
            'selectedCategory' => $categoryId,
            // ... kirimkan juga data filter lainnya
        ]));
    }


    // ----------------------------------------------------------------------
    // 2. API ENDPOINTS (Diberi suffix 'Api' dan mengembalikan JSON)
    // Digunakan untuk AJAX/Mobile API jika diperlukan
    // ----------------------------------------------------------------------

    /**
     * [API] Mengambil daftar menu yang sudah disetujui.
     * GET /api/v1/menus
     */
    public function indexApi() {
        $menus = MenuMakanan::with(['restoran', 'kategori'])
            ->where('status_verifikasi', 'disetujui')
            ->get();
            
        return response()->json($menus);
    }

    /**
     * [API] Mengambil detail menu, hanya jika menu tersebut sudah disetujui.
     * GET /api/v1/menus/{id}
     */
    public function showApi($id) {
        $menu = MenuMakanan::with(['restoran', 'kategori'])
            ->where('status_verifikasi', 'disetujui')
            ->findOrFail($id);
            
        return response()->json($menu);
    }
}