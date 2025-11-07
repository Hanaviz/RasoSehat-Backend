<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ Tambahkan baris ini

class KategoriMakananSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori_makanan')->insert([
            ['nama_kategori' => 'Rendah Kalori', 'deskripsi' => 'Makanan rendah kalori.'],
            ['nama_kategori' => 'Rendah Gula', 'deskripsi' => 'Makanan rendah gula.'],
            ['nama_kategori' => 'Tinggi Protein', 'deskripsi' => 'Makanan tinggi protein.'],
            ['nama_kategori' => 'Seimbang', 'deskripsi' => 'Makanan bergizi seimbang.'],
            ['nama_kategori' => 'Vegetarian/Vegan', 'deskripsi' => 'Tanpa bahan hewani.'],
        ]);
    }
}
