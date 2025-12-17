<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcilePendingMidtrans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:reconcile-midtrans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile pending Midtrans payments by checking status and updating transactions accordingly';

    protected MidtransService $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        parent::__construct();
        $this->midtrans = $midtrans;
    }

    public function handle()
    {
        $this->info('Reconciling pending Midtrans payments...');

        $payments = Payment::where('payment_gateway', 'midtrans')
            ->where('status', Payment::STATUS_PENDING)
            ->where('created_at', '<', now()->subMinutes(5))
            ->with('transaction')
            ->get();

        foreach ($payments as $payment) {
            $orderId = $payment->transaction_id;
            $this->info('Checking order: ' . $orderId);
            try {
                $status = $this->midtrans->getTransactionStatus($orderId);
                $map = [
                    'capture' => Payment::STATUS_SUCCESS,
                    'settlement' => Payment::STATUS_SUCCESS,
                    'pending' => Payment::STATUS_PENDING,
                    'deny' => Payment::STATUS_FAILED,
                    'expire' => Payment::STATUS_EXPIRED,
                    'cancel' => Payment::STATUS_CANCELLED,
                ];

                $midtransStatus = $status['transaction_status'] ?? ($status['status'] ?? null);
                $paymentStatus = $map[$midtransStatus] ?? null;
                if ($paymentStatus) {
                    $payment->update([
                        'status' => $paymentStatus,
                        'response_payload' => $status,
                        'payment_method' => $status['payment_type'] ?? $payment->payment_method,
                        'gateway_reference' => $status['transaction_id'] ?? $payment->gateway_reference,
                        'paid_at' => in_array($paymentStatus, [Payment::STATUS_SUCCESS]) ? now() : $payment->paid_at,
                    ]);

                    if ($paymentStatus === Payment::STATUS_SUCCESS) {
                        $payment->transaction->update(['status' => 'paid']);
                    }
                    if (in_array($paymentStatus, [Payment::STATUS_EXPIRED, Payment::STATUS_CANCELLED])) {
                        $payment->transaction->update(['status' => 'cancelled']);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Reconcile pending payment failed for order ' . $orderId, ['error' => $e->getMessage()]);
            }
        }

        $this->info('Reconciliation complete.');
        return 0;
    }
}
