<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ubah User:create menjadi User::create
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',// <--- ATUR USERNAME DI SINI
            'password' => Hash::make('password123'), // <--- ATUR PASSWORD DI SINI (Wajib di-Hash)
        ]);

        // Anda bisa menambah user lain di bawahnya jika mau
    }
}
