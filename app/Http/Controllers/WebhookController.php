<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function midtrans(Request $request)
    {
        try {
            $serverKey = env('MIDTRANS_SERVER_KEY');
            $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

            // Verify signature if available
            if (isset($request->signature_key) && $request->signature_key !== $hashed) {
                Log::warning('Invalid Midtrans signature', ['order_id' => $request->order_id]);
                return response()->json(['status' => 'error'], 403);
            }

            $transaction = Transaction::findOrFail($request->order_id);


            // Map to payment statuses and update/create payment record
            $statusMap = [
                'capture' => Payment::STATUS_SUCCESS,
                'settlement' => Payment::STATUS_SUCCESS,
                'pending' => Payment::STATUS_PENDING,
                'deny' => Payment::STATUS_FAILED,
                'expire' => Payment::STATUS_EXPIRED,
                'cancel' => Payment::STATUS_CANCELLED,
            ];

            $midtransStatus = $request->transaction_status ?? null;
            $paymentStatus = $statusMap[$midtransStatus] ?? null;

            // Find a pending payment for this transaction or create one
            $payment = $transaction->payments()->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_FAILED])->latest()->first();
            if (! $payment) {
                $payment = Payment::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'payment_method' => 'midtrans',
                    'payment_method' => $request->payment_type ?? null,
                    'payment_gateway' => 'midtrans',
                    // Prefer Midtrans transaction_id (gateway's unique id) if present
                    'gateway_reference' => $request->transaction_id ?? $request->order_id ?? $transaction->id,
                    'snap_token' => null,
                    'amount' => $transaction->total_amount,
                    'status' => $paymentStatus ?? Payment::STATUS_PENDING,
                    'response_payload' => $request->all(),
                ]);
            } else {
                $payment->update([
                    'status' => $paymentStatus ?? $payment->status,
                    'response_payload' => $request->all(),
                    'payment_method' => $request->payment_type ?? $payment->payment_method,
                    'gateway_reference' => $request->transaction_id ?? $payment->gateway_reference,
                    'paid_at' => in_array($paymentStatus, [Payment::STATUS_SUCCESS]) ? now() : $payment->paid_at,
                ]);
            }

            if ($payment->status === Payment::STATUS_SUCCESS) {
                $transaction->update(['status' => 'paid']);
            }

            if (in_array($payment->status, [Payment::STATUS_EXPIRED, Payment::STATUS_CANCELLED])) {
                $transaction->update(['status' => 'cancelled']);
            }

            Log::info('Midtrans webhook processed', [
                'order_id' => $request->order_id,
                'status' => $payment->status,
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Midtrans webhook error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }
    }
}
