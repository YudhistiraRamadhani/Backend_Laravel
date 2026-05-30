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
        Schema::create('transaksi_pengeluaran_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
        $table->text('deskripsi')->nullable();
        $table->bigInteger('harga',);
        $table->integer('jumlah');
        $table->date('tanggal_transaksi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_pengeluaran_barangs');
    }
};
