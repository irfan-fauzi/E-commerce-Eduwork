<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $i => $user) {
            Address::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'label' => $i === 0 ? 'Kantor' : 'Rumah',
                    'recipient_name' => $user->name,
                    'phone' => $user->phone ?? null,
                    'address_text' => 'Jalan Contoh No. ' . ($i + 1),
                    'city' => 'Bandung',
                    'province' => 'Jawa Barat',
                    'postal_code' => '40135',
                    'is_primary' => $i === 0,
                ]
            );
        }
    }
}
