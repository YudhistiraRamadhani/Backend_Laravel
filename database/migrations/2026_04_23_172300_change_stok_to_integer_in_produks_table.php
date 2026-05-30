<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Menggunakan raw SQL agar bisa menyertakan klausa USING
        DB::statement('ALTER TABLE produks ALTER COLUMN "Stok" TYPE integer USING "Stok"::integer');
    }

    public function down(): void
    {
        // Mengembalikan ke tipe data string jika perlu
        DB::statement('ALTER TABLE produks ALTER COLUMN "Stok" TYPE varchar(255)');
    }
};
