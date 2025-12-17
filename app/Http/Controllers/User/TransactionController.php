<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Allow checking Midtrans transaction status and cancelling
 */

class TransactionController extends Controller
{
    /**
     * Display the specified transaction (order).
     * User can only view their own transactions.
     */
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function show(Transaction $transaction)
    {
        // Check if transaction belongs to authenticated user
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized. This order does not belong to you.');
        }

        // Load transaction with relationships
        $transaction->load('items.product', 'address', 'user');

        // If transaction is pending, check with Midtrans whether it expired
        if ($transaction->status === 'pending') {
            try {
                $statusResponse = $this->midtransService->getTransactionStatus($transaction->id);
                $midtransStatus = $statusResponse['transaction_status'] ?? ($statusResponse['status'] ?? null);

                // If Midtrans reports expire/cancel, update locally and restore stock
                if (in_array($midtransStatus, ['expire', 'cancel', 'deny'])) {
                    $transaction->update(['status' => 'cancelled']);

                    // mark any pending payments as expired/cancelled
                    foreach ($transaction->payments()->where('status', Payment::STATUS_PENDING)->get() as $payment) {
                        $payment->update(['status' => $midtransStatus === 'expire' ? Payment::STATUS_EXPIRED : Payment::STATUS_CANCELLED]);
                    }

                    foreach ($transaction->items as $item) {
                        if ($item->product) {
                            $item->product->increment('stock', $item->quantity);
                        }
                    }
                }
            } catch (\Exception $e) {
                // If API call fails, silently continue — we don't want to block the user view.
            }
            // refresh after possible status change and reload relationships
            $transaction->refresh();
            $transaction->load('items.product', 'address', 'user');
        }

        return view('user.transactions.show', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Cancel a pending transaction by the user.
     */
    public function cancel(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        if ($transaction->status !== 'pending') {
            return redirect()->route('user.transactions.show', $transaction)
                ->with('error', 'Only pending transactions can be cancelled.');
        }

        // Try to cancel remotely first (best-effort) and cancel any pending payment records
        try {
            $this->midtransService->cancelTransaction($transaction->id);
        } catch (\Exception $e) {
            // ignore
        }

        // Cancel any pending payments
        foreach ($transaction->payments()->where('status', Payment::STATUS_PENDING)->get() as $payment) {
            $payment->update([
                'status' => 'cancelled',
                'failure_reason' => 'Cancelled by user',
            ]);
        }

        // Update local status and restore stock
        $transaction->update(['status' => 'cancelled']);
        foreach ($transaction->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        return redirect()->route('user.transactions.show', $transaction)
            ->with('success', 'Transaction cancelled successfully.');
    }
}
