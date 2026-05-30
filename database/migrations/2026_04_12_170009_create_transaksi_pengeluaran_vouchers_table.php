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
        Schema::create('transaksi_pengeluaran_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('nama_barang');
            $table->string('jenis');
            $table->date('tanggal_transaksi');
            $table->integer('jumlah');
            $table->bigInteger('harga');
            $table->string('nama_supplier');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_pengeluaran_vouchers');
    }
};
