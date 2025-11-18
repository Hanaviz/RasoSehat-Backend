<?php

namespace App\Http\Controllers;

use App\Models\MenuMakanan;
use App\Models\KategoriMakanan; // Diperlukan untuk filter kategori
use Illuminate\Http\Request;

class PublicMenuController extends Controller
{
    /**
     * Mengambil daftar menu yang sudah disetujui (VERIFIED ONLY).
     * Dapat digunakan untuk halaman utama dan halaman kategori.
     */
    public function index() {
        // FILTER KRITIS: Hanya tampilkan menu yang statusnya 'disetujui'
        $menus = MenuMakanan::with(['restoran', 'kategori'])
            ->where('status_verifikasi', 'disetujui')
            ->get();
            
        return response()->json($menus);
    }

    /**
     * Mengambil detail menu, hanya jika menu tersebut sudah disetujui.
     */
    public function show($id) {
        // FILTER KRITIS: Cari menu yang disetujui berdasarkan ID
        $menu = MenuMakanan::with(['restoran', 'kategori'])
            ->where('status_verifikasi', 'disetujui')
            ->findOrFail($id);
            
        return response()->json($menu);
    }

    /**
     * Fungsi Pencarian dan Filter (Diperlukan oleh SearchResultsPage.jsx)
     * Query Params: ?q=query&category=id_kategori
     */
    public function search(Request $request)
    {
        $query = $request->input('q'); // Kata kunci pencarian
        $categoryId = $request->input('category_id'); // Filter berdasarkan ID Kategori
        $minRating = $request->input('min_rating'); // Filter Rating Minimum (misal: 4.0)

        $menus = MenuMakanan::with(['restoran', 'kategori', 'ulasan'])
            // 1. FILTER WAJIB: Hanya yang sudah disetujui
            ->where('status_verifikasi', 'disetujui')
            
            // 2. Filter Kata Kunci (Nama Menu atau Deskripsi)
            ->when($query, function ($q) use ($query) {
                $q->where('nama_menu', 'like', '%' . $query . '%')
                  ->orWhere('deskripsi', 'like', '%' . $query . '%')
                  // Tambahkan pencarian di kolom bahan_baku dan diet_claims
                  ->orWhere('bahan_baku', 'like', '%' . $query . '%')
                  ->orWhereJsonContains('diet_claims', $query);
            })
            
            // 3. Filter Kategori
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('kategori_id', $categoryId);
            })
            
            // 4. Filter Rating Minimum (Perlu perhitungan di Model atau join/subquery)
            // Untuk kesederhanaan, kita akan memfilter di tingkat database 
            // Jika Anda memiliki rata-rata rating yang tersimpan
            // ->when($minRating, function ($q) use ($minRating) {
            //     // Asumsi: Anda memiliki kolom average_rating
            //     $q->where('average_rating', '>=', $minRating); 
            // })
            
            ->orderByDesc('kalori') // Contoh sorting
            ->get();
            
        return response()->json($menus);
    }
}