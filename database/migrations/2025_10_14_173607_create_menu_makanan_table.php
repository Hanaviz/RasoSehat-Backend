<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('menu_makanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restoran_id')->constrained('restorans')->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategori_makanan')->onDelete('cascade');
            $table->string('nama_menu', 100);
            $table->text('deskripsi')->nullable();
            $table->integer('kalori')->default(0);
            $table->decimal('protein', 5, 2)->default(0);
            $table->decimal('gula', 5, 2)->default(0);
            $table->decimal('lemak', 5, 2)->default(0);
            $table->decimal('harga', 10, 2);
            $table->string('foto')->nullable();
            $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('menu_makanan');
    }
};