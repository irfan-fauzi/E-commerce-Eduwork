<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Address;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];
        foreach (range(1, 20) as $n) {
            $buyer = $users->random();
            $items = $products->random(rand(1, 4));
            $subtotal = 0;
            // pick a random address for the buyer if exists
            $address = $buyer->addresses()->inRandomOrder()->first();

            $transaction = Transaction::create([
                'user_id' => $buyer->id,
                'address_id' => $address ? $address->id : null,
                'status' => $statuses[array_rand($statuses)],
                'total_amount' => 0,
                'shipping_fee' => 10000,
            ]);

            foreach ($items as $item) {
                $qty = rand(1, 3);
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->id,
                    'product_sku' => $item->sku ?? null,
                    'product_name' => $item->name,
                    'quantity' => $qty,
                    'price' => $item->price,
                ]);
                $subtotal += $item->price * $qty;
            }

            $transaction->total_amount = $subtotal + $transaction->shipping_fee;
            $transaction->save();
        }
    }
}
