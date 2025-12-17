<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Product;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Create 20 cart items across users
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $product = $products->random();
            CartItem::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ], [
                'quantity' => rand(1, 3),
                'added_at' => now(),
            ]);
        }
    }
}
