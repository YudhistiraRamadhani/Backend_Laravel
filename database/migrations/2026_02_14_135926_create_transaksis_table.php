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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('Nama_Barang');
            $table->string('Harga');
            $table->string('Jumlah');
            $table->date('Tanggal');
            $table->string('nama_supplier')->nullable();


            // Menggunakan enum agar data konsisten (Pemasukan/Pengeluaran)
            $table->enum('jenis_transaksi', ['Pemasukan', 'Pengeluaran']);

            // Menggunakan string biasa untuk fleksibilitas (Barang/Voucher/dll)
            $table->string('jenis_barang', 255);
$table->text('deskripsi')->nullable();
            $table->timestamps();
        }); // Tutup fungsi Schema
    } // Tutup fungsi up

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
