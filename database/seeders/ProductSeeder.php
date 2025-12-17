<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $baseNames = [
            'Televisi LED 43"', 'Kaos Polos Pria', 'Set Panci Stainless', 'Pelembap Wajah', 'Sepatu Lari',
            'Boneka Teddy Bear', 'Novel Indonesia', 'Kopi Luwak Sachet', 'Smartphone X Pro', 'Dompet Kulit'
        ];

        $categories = Category::all();
        if ($categories->isEmpty()) {
            return;
        }

        // Ensure 20 products
        for ($i = 0; $i < 20; $i++) {
            $pname = $baseNames[$i % count($baseNames)];
            if ($i >= count($baseNames)) {
                $pname .= ' ' . ($i + 1);
            }
            $slug = 'produk-' . ($i + 1);
            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'sku' => 'SKU' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'name' => $pname,
                    'short_description' => "Deskripsi singkat untuk $pname.",
                    'description' => "Deskripsi lengkap untuk $pname.",
                    'price' => rand(50000, 500000),
                    'stock' => rand(10, 200),
                    'category_id' => $categories[$i % $categories->count()]->id,
                    'active' => true,
                ]
            );

            if (! ProductImage::where('product_id', $product->id)->exists()) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => 'products/sample-' . ($i + 1) . '.jpg',
                    'position' => 0,
                    'alt_text' => $pname,
                ]);
            }
        }
    }
}
