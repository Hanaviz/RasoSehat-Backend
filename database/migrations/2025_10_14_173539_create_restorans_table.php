<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('restorans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_restoran', 100);
            $table->text('deskripsi')->nullable();
            $table->text('alamat');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->enum('jenis_usaha', ['perorangan', 'korporasi'])->default('perorangan');
            $table->string('foto_ktp')->nullable();
            $table->string('npwp')->nullable();
            $table->string('dokumen_usaha')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('restorans');
    }
};
