<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'martinezmorenojosue29@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('12345678'),
                'rol' => 'Administrador',
                'email_verified_at' => now(),
                'is_active' => true,
                'is_super_admin' => true,
            ]
        );
    }
}
