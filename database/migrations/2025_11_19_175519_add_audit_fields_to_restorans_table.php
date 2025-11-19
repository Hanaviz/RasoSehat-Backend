<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('restorans', function (Blueprint $table) {
            // Kolom-kolom yang diperlukan untuk Controller@store
            $table->text('health_focus')->after('jenis_usaha')->nullable()->comment('Fokus kesehatan utama toko (Rendah Gula, dll)');
            $table->string('dominant_fat', 50)->after('health_focus')->nullable()->comment('Jenis minyak dominan yang digunakan');
            $table->text('dominant_cooking_method')->after('dominant_fat')->nullable()->comment('Metode masak yang diprioritaskan');
            $table->string('sales_channels', 200)->after('media_sosial')->nullable()->comment('Saluran penjualan/pemesanan (WA, GoFood, dll)');
            
            // Catatan: Kolom jam_operasional dan media_sosial diabaikan di sini
            // karena diasumsikan sudah ada di migration awal 2025_10_14_173539_create_restorans_table.php
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('restorans', function (Blueprint $table) {
            $table->dropColumn(['health_focus', 'dominant_fat', 'dominant_cooking_method', 'sales_channels']);
        });
    }
};