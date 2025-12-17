<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wishlist;
use App\Models\User;
use App\Models\Product;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        // create 20 wishlist entries across users
        $created = 0;
        while ($created < 20) {
            $user = $users->random();
            $product = $products->random();
            $entry = Wishlist::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            if ($entry->wasRecentlyCreated) {
                $created++;
            }
        }
    }
}
