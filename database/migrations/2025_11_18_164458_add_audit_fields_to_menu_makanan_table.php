<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('menu_makanan', function (Blueprint $table) {
            // Field-field yang dibutuhkan untuk Audit Lite / Verifikasi Berbasis Bahan Baku
            $table->text('bahan_baku')->after('deskripsi')->nullable()->comment('Daftar bahan baku mentah yang disubmit penjual');
            $table->string('metode_masak', 100)->after('bahan_baku')->nullable()->comment('Metode masak utama (Panggang/Kukus/Rebus) untuk verifikasi lemak');
            $table->json('diet_claims')->after('metode_masak')->nullable()->comment('Klaim diet yang dipilih penjual (Rendah Kalori, Tinggi Protein, dll.)');
            
            // Field untuk menyimpan feedback admin jika menu ditolak
            $table->text('catatan_admin')->after('status_verifikasi')->nullable()->comment('Alasan penolakan atau catatan dari admin');

            // Tambahkan kolom nutrisi tambahan dari frontend
            $table->decimal('serat', 5, 2)->after('lemak')->default(0)->nullable();
            $table->decimal('lemak_jenuh', 5, 2)->after('serat')->default(0)->nullable();
        });
    }

    public function down(): void {
        Schema::table('menu_makanan', function (Blueprint $table) {
            $table->dropColumn(['bahan_baku', 'metode_masak', 'diet_claims', 'catatan_admin', 'serat', 'lemak_jenuh']);
        });
    }
};