<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $transactions = Transaction::all();
        $users = User::all()->keyBy('id');

        foreach ($transactions as $t) {
            // Ensure there isn't already a payment
            if (Payment::where('transaction_id', $t->id)->exists()) {
                continue;
            }

                $userId = $t->user_id;
                // Determine simple mapping for status
                $paymentStatus = Payment::STATUS_PENDING;
                if (in_array($t->status, ['paid', 'completed'])) {
                    $paymentStatus = Payment::STATUS_SUCCESS;
                } elseif ($t->status === 'cancelled') {
                    $paymentStatus = Payment::STATUS_CANCELLED;
                } elseif (rand(1, 10) === 1) {
                    $paymentStatus = Payment::STATUS_FAILED;
                }

                Payment::create([
                    'transaction_id' => $t->id,
                    'user_id' => $userId,
                    'payment_method' => 'midtrans',
                    'payment_method' => 'online',
                    'payment_gateway' => 'midtrans',
                    'gateway_reference' => 'gw_' . $t->id,
                    'snap_token' => null,
                    'amount' => $t->total_amount,
                    'status' => $paymentStatus,
                    'response_payload' => null,
                    'failure_reason' => null,
                    'paid_at' => $paymentStatus === Payment::STATUS_SUCCESS ? now() : null,
                ]);
        }
    }
}
