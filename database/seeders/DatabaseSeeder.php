<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin utama
        // Password otomatis di-hash oleh model (casts: 'password' => 'hashed')
        User::updateOrCreate(
            ['email' => 'adminkeuangan@gmail.com'],
            [
                'name' => 'Admin Keuangan KKNM',
                'password' => 'admin123',  // Jangan pakai Hash::make()!
                'role' => 'super_admin',
            ]
        );
    }
}

