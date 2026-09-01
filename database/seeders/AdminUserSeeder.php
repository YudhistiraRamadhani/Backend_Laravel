<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    \App\Models\User::updateOrCreate(
        ['email' => 'projek_aplikasi@gmail.com'],
        [
            'name' => 'Admin',
            'username' => 'projek_aplikasi',
            'password' => bcrypt('1'),
        ]
    );
}
}
