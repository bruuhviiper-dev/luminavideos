<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrador',
            'username' => 'admin',
            'email' => 'admin@tubiii.com',
            'password' => Hash::make('admin123'),
            'bio' => 'Administrador do Tubiii',
            'is_verified' => true,
            'is_admin' => true,
        ]);

        // Test users
        User::factory()->count(5)->create();
    }
}
