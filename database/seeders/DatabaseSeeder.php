<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\UserSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\AddressSeeder;
use Database\Seeders\TransactionSeeder;
use Database\Seeders\PaymentSeeder;
use Database\Seeders\CartItemSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // If not production, truncate related tables for a clean slate.
        if (!app()->environment('production')) {
            // Temporarily disable foreign key checks (MySQL) to allow truncation order
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            $tables = [
                'transaction_items',
                'payments',
                'transactions',
                'product_images',
                'products',
                'cart_items',
                'wishlists',
                'addresses',
                'categories',
                'users',
            ];
            foreach ($tables as $t) {
                if (Schema::hasTable($t)) {
                    DB::table($t)->truncate();
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        // Run the modular seeders in order to ensure foreign key integrity.
            $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            AddressSeeder::class,
            TransactionSeeder::class,
            PaymentSeeder::class,
            CartItemSeeder::class,
                WishlistSeeder::class,
        ]);
    }
}
