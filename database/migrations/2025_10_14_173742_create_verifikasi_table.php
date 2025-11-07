<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipe_objek', ['restoran', 'menu']);
            $table->unsignedBigInteger('objek_id');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_verifikasi')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('verifikasi');
    }
};
