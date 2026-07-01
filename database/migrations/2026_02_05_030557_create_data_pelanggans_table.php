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
        Schema::create('data_pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan');
            $table->string('no_whatsapp');
               $table->string('pesannotifikasi')->nullable();
  $table->string('telegram_chat_id')->nullable()->after('pesannotifikasi');
    $table->string('telegram_username')->nullable()->after('telegram_chat_id');
    $table->boolean('telegram_active')->default(false)->after('telegram_username');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pelanggans');
    }
};
