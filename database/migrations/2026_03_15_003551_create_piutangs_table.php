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
        Schema::create('piutang', function (Blueprint $table) {
        $table->id();
        $table->string('nama_pelanggan', 100);
        $table->integer('jumlah_hutang');
        $table->string('nama_barang', 100);
        $table->integer('harga');
        $table->string('status', 50);
        $table->string('no_whatsapp', 255);
        $table->date('date')->nullable();
        $table->text('pesanpenagihan')->nullable();
         $table->string('telegram_chat_id')->nullable()->after('no_whatsapp');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piutangs');
    }
};
