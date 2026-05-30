<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesan_notifikasi_penagihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan', 100);
            $table->string('nama_barang', 100);
            $table->string('nomorwa', 20);
            $table->text('pesan_notifikasi_penagihan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesan_notifikasi_penagihans');
    }
};
