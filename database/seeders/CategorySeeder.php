<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $base = ['Elektronik', 'Pakaian', 'Rumah Tangga', 'Kecantikan', 'Olahraga', 'Mainan', 'Buku', 'Makanan & Minuman', 'Gadget', 'Aksesoris'];
        // Ensure 20 categories
        for ($i = 0; $i < 20; $i++) {
            $name = $base[$i % count($base)];
            if ($i >= count($base)) {
                $name .= ' ' . ($i + 1);
            }
            $slug = strtolower(str_replace(' ', '-', $name));
            Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => "Kategori produk $name"]
            );
        }
    }
}
