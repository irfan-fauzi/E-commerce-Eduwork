<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usersData = [
            ['name' => 'Administrator', 'email' => 'admin@gmail.com', 'phone' => '081100000000', 'is_admin' => true],
            ['name' => 'Budi Santoso', 'email' => 'budi.santoso@gmail.com', 'phone' => '081200000001', 'is_admin' => false],
            ['name' => 'Siti Aminah', 'email' => 'siti.aminah@gmail.com', 'phone' => '081200000002', 'is_admin' => false],
            ['name' => 'Andi Wijaya', 'email' => 'andi.wijaya@gmail.com', 'phone' => '081200000003', 'is_admin' => false],
            ['name' => 'Rini Putri', 'email' => 'rini.putri@gmail.com', 'phone' => '081200000004', 'is_admin' => false],
            ['name' => 'Dewi Lestari', 'email' => 'dewi.lestari@gmail.com', 'phone' => '081200000005', 'is_admin' => false],
            ['name' => 'Tono Prasetyo', 'email' => 'tono.prasetyo@gmail.com', 'phone' => '081200000006', 'is_admin' => false],
            ['name' => 'Rudi Hartono', 'email' => 'rudi.hartono@gmail.com', 'phone' => '081200000007', 'is_admin' => false],
            ['name' => 'Maya Kurnia', 'email' => 'maya.kurnia@gmail.com', 'phone' => '081200000008', 'is_admin' => false],
            ['name' => 'Arif Nugroho', 'email' => 'arif.nugroho@gmail.com', 'phone' => '081200000009', 'is_admin' => false],
        ];

        foreach ($usersData as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'phone' => $u['phone'],
                    'is_admin' => $u['is_admin'],
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
