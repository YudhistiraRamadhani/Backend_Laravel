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
        Schema::create('notification_fonntepromos', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nomorwa');
            $table->text('pesannotifikasi');
            $table->string('namabarang');
            $table->date('tanggal_kirim')->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_fonntepromos');
    }
};
