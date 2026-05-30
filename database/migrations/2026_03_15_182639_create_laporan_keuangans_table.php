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
        Schema::create('laporan_keuangan', function (Blueprint $table) {
            $table->id();
            // Primary Key
        $table->integer('pendapatan')->default(0);
        $table->integer('pengeluaran')->default(0);
        $table->string('nama_barang', 100);
        $table->string('nama_supplier', 100)->nullable(); // Tambah ini

            $table->integer('harga');
            $table->integer('jumlah');
   
        $table->string('jenis_barang', 255);
        $table->date('tanggal');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_keuangans');
    }
};
