<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Generate a unique order id for Midtrans to avoid duplicate order_id errors.
     */
    protected function generateUniqueOrderId($transactionId)
    {
        return $transactionId . '-' . bin2hex(random_bytes(5));
    }

    public function index()
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product', 'product.images')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.cart.index')->with('error', 'Cart is empty.');
        }

        // Ambil alamat via query agar tidak men-trigger static analyzer
        $addresses = \App\Models\Address::where('user_id', Auth::id())->get();
        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return view('user.checkout.index', compact('cartItems', 'addresses', 'total'));
    }

    public function pay(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'shipping_fee' => 'required|integer|min:0',
            'message' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.cart.index')->with('error', 'Cart is empty.');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        $total = $subtotal + $validated['shipping_fee'];

        // NOTE: Do not attempt to contact Midtrans during checkout submission.
        // We only create the Transaction and a pending Payment record here.

        $transaction = DB::transaction(function () use ($user, $cartItems, $validated, $subtotal, $total) {
            // Create transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'address_id' => $validated['address_id'],
                'status' => 'pending',
                'total_amount' => $total,
                'shipping_fee' => $validated['shipping_fee'],
                'message' => $validated['message'] ?? null,
            ]);

            // Create transaction items
            foreach ($cartItems as $cartItem) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $cartItem->product_id,
                    'product_sku' => $cartItem->product->sku,
                    'product_name' => $cartItem->product->name,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);


            }


            // Force Midtrans as the payment gateway for all orders. We DO NOT call Midtrans here.

            // Create a pending payment attempt. Snap token and gateway-specific details
            // will be created/updated when the user visits the payment page and clicks Pay.
            $payment = Payment::create([
                'transaction_id' => $transaction->id,
                'user_id' => Auth::id(),
                'payment_method' => null,
                'payment_gateway' => 'midtrans',
                'gateway_reference' => (string) $transaction->id,
                'snap_token' => null,
                'amount' => $total,
                'status' => Payment::STATUS_PENDING,
            ]);

            // Do not clear the cart here. Cart items will be removed when payment is successful
            // to allow users to retry payment without losing their cart if payment fails.
            return $transaction;
        });

        $transaction->refresh();
        $transaction->load('payments');

        // If the request expects JSON (AJAX), return minimal data including payment URL
        if ($request->wantsJson()) {
            $lastPayment = $transaction->payments->last();
            return response()->json([
                'transaction_id' => $transaction->id,
                'payment_url' => route('user.checkout.payment.show', $transaction),
                'amount' => $lastPayment->amount ?? $transaction->total_amount,
            ]);
        }

        // Redirect user to the transaction payment page where they can click to pay
        return redirect()->route('user.checkout.payment.show', $transaction)->with('success', 'Order created. Silakan lanjutkan ke pembayaran.');
    }

    /**
     * Show payment page for an existing transaction (allow continue payment)
     */
    public function showPayment(Transaction $transaction)
    {
        // ensure the current user owns the transaction
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized. This order does not belong to you.');
        }

        // Only allow showing payment page for pending transactions
        if ($transaction->status !== 'pending') {
            return redirect()->route('user.transactions.show', $transaction)
                ->with('error', 'Payment cannot be continued for this transaction.');
        }

        $transaction->load('items.product', 'address', 'user', 'payments');

        // If there is a pending payment, reuse it. Otherwise create a fresh payment attempt (new snap token).
        // If a pending payment exists but has no snap token, attempt to generate one and update it.
        $pending = $transaction->payments()->where('status', Payment::STATUS_PENDING)->latest()->first();
            if ($pending) {
            if (empty($pending->snap_token) && $pending->payment_gateway === 'midtrans') {
                // Prefer using whatever gateway_reference was previously recorded, fall back to transaction id
                $gatewayReference = $pending->gateway_reference ?? (string) $transaction->id;
                try {
                    $newToken = $this->midtransService->createSnapToken($gatewayReference, $transaction->total_amount, $transaction->user);
                    $pending->update(['snap_token' => $newToken]);
                    \Illuminate\Support\Facades\Log::info('Midtrans snap token updated for pending payment', ['transaction_id' => $transaction->id, 'gateway_reference' => $gatewayReference]);
                    $transaction->load('payments');
                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    // If the Midtrans API indicates the order_id already exists, retry with another unique id
                    $body = $e->getResponse() ? (string) $e->getResponse()->getBody() : '';
                    if (str_contains($body, 'order_id has already been taken')) {
                        try {
                            $gatewayReference = $this->generateUniqueOrderId($transaction->id);
                            $newToken = $this->midtransService->createSnapToken($gatewayReference, $transaction->total_amount, $transaction->user);
                            $pending->update(['snap_token' => $newToken, 'gateway_reference' => $gatewayReference]);
                            \Illuminate\Support\Facades\Log::info('Midtrans snap token and gateway_reference updated for pending payment', ['transaction_id' => $transaction->id, 'gateway_reference' => $gatewayReference]);
                            $transaction->load('payments');
                        } catch (\Exception $e2) {
                            \Illuminate\Support\Facades\Log::error('Unable to generate Midtrans snap token after retry in showPayment', ['error' => $e2->getMessage(), 'order_id' => $transaction->id]);
                            session()->flash('error', 'Payment gateway currently unavailable. Please contact support.');
                        }
                    } else {
                        \Illuminate\Support\Facades\Log::error('Unable to generate Midtrans snap token in showPayment', ['error' => $e->getMessage(), 'order_id' => $transaction->id]);
                        $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : null;
                        if ($status === 401) {
                            session()->flash('error', 'Payment gateway authentication failed. Please check MIDTRANS_SERVER_KEY and MIDTRANS_CLIENT_KEY.');
                        } else {
                            session()->flash('error', 'Payment gateway currently unavailable. Please contact support.');
                        }
                    }
                } catch (\RuntimeException $e) {
                    // Show explicit runtime errors (e.g. missing keys)
                    \Illuminate\Support\Facades\Log::error('Midtrans runtime error in showPayment', ['error' => $e->getMessage(), 'order_id' => $transaction->id]);
                    session()->flash('error', $e->getMessage());
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Unable to generate Midtrans snap token in showPayment', ['error' => $e->getMessage(), 'order_id' => $transaction->id]);
                    session()->flash('error', 'Payment gateway currently unavailable. Please contact support.');
                }
            }
        } else {
            if ($transaction->status === 'pending') {
                $gatewayReference = (string) $transaction->id;
                $newToken = null;
                try {
                    $newToken = $this->midtransService->createSnapToken($gatewayReference, $transaction->total_amount, $transaction->user);
                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    $body = $e->getResponse() ? (string) $e->getResponse()->getBody() : '';
                    if (str_contains($body, 'order_id has already been taken')) {
                        try {
                            $gatewayReference = $this->generateUniqueOrderId($transaction->id);
                            $newToken = $this->midtransService->createSnapToken($gatewayReference, $transaction->total_amount, $transaction->user);
                        } catch (\Exception $e2) {
                            \Illuminate\Support\Facades\Log::error('Unable to create Midtrans payment attempt after retry in showPayment', ['error' => $e2->getMessage(), 'order_id' => $transaction->id]);
                            session()->flash('error', 'Payment gateway currently unavailable. Please contact support.');
                        }
                    } else {
                        \Illuminate\Support\Facades\Log::error('Unable to create Midtrans payment attempt in showPayment', ['error' => $e->getMessage(), 'order_id' => $transaction->id]);
                        session()->flash('error', 'Payment gateway currently unavailable. Please contact support.');
                    }
                } catch (\RuntimeException $e) {
                    \Illuminate\Support\Facades\Log::error('Midtrans runtime error creating payment in showPayment', ['error' => $e->getMessage(), 'order_id' => $transaction->id]);
                    session()->flash('error', $e->getMessage());
                }

                if ($newToken) {
                    $pending = Payment::create([
                        'transaction_id' => $transaction->id,
                        'user_id' => Auth::id(),
                        'payment_method' => null,
                        'payment_gateway' => 'midtrans',
                        'gateway_reference' => $gatewayReference,
                        'snap_token' => $newToken,
                        'amount' => $transaction->total_amount,
                        'status' => Payment::STATUS_PENDING,
                    ]);
                    \Illuminate\Support\Facades\Log::info('Midtrans payment attempt created', ['transaction_id' => $transaction->id, 'gateway_reference' => $gatewayReference]);
                    $transaction->load('payments');
                }
            }
        }

        $lastPayment = $transaction->payments->last();
        $autoOpen = $lastPayment && $lastPayment->payment_gateway === 'midtrans' && !empty($lastPayment->snap_token);
        return view('user.checkout.payment', compact('transaction', 'autoOpen'));
    }

    public function notifyPayment(Request $request)
    {
        // Accept either a numeric transaction id or the Midtrans order id (gateway_reference)
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $orderId = (string) $request->order_id;

        // Find the transaction either by numeric ID or by a payment with the gateway_reference.
        // For webhook notifications we should allow unauthenticated requests (server-to-server),
        // but preserve ownership checks when the requester is an authenticated user.
        $transaction = null;
        if (ctype_digit($orderId)) {
            $transaction = Transaction::find((int)$orderId);
        }

        if (! $transaction) {
            $payment = Payment::where('gateway_reference', $orderId)->first();
            if ($payment) {
                $transaction = $payment->transaction;
            }
        }

        if (! $transaction) {
            return response()->json(['error' => 'Transaction not found.'], 404);
        }

        // If the request is authenticated, ensure the authenticated user owns the transaction.
        if (Auth::check() && $transaction->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        // Determine the Midtrans order id we should query (use gateway_reference if present)
        $midtransOrderId = $orderId;
        $latestPayment = $transaction->payments()->latest()->first();
        if ($latestPayment && $latestPayment->gateway_reference) {
            $midtransOrderId = $latestPayment->gateway_reference;
        }

        // Verify transaction status from Midtrans API
        try {
            $statusResponse = $this->midtransService->getTransactionStatus($midtransOrderId);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to verify payment status.'], 500);
        }

        // Mapping Midtrans statuses to payment statuses
        $statusMap = [
            'capture' => Payment::STATUS_SUCCESS,
            'settlement' => Payment::STATUS_SUCCESS,
            'pending' => Payment::STATUS_PENDING,
            'deny' => Payment::STATUS_FAILED,
            'expire' => Payment::STATUS_EXPIRED,
            'cancel' => Payment::STATUS_CANCELLED,
        ];

        $midtransStatus = $statusResponse['transaction_status'] ?? ($statusResponse['status'] ?? null);
        $paymentStatus = $statusMap[$midtransStatus] ?? null;

        // First try to find a payment matching the Midtrans order id, otherwise reuse a pending/failed attempt
        $payment = $transaction->payments()->where('gateway_reference', $midtransOrderId)->latest()->first();
        if (! $payment) {
            $payment = $transaction->payments()->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_FAILED])->latest()->first();
        }

        if (! $payment) {
            // If no payment exists, create a record from the notification
            $payment = Payment::create([
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'payment_method' => $statusResponse['payment_type'] ?? null,
                'payment_gateway' => 'midtrans',
                'gateway_reference' => $statusResponse['transaction_id'] ?? $midtransOrderId,
                'snap_token' => null,
                'amount' => $transaction->total_amount,
                'status' => $paymentStatus ?? Payment::STATUS_PENDING,
                'response_payload' => $statusResponse,
            ]);
        }

        // Update payment with response payload and mapped status
        $payment->update([
            'status' => $paymentStatus ?? $payment->status,
            'response_payload' => $statusResponse,
            // Update `payment_method` with gateway's payment_type when available
            'payment_method' => $statusResponse['payment_type'] ?? $payment->payment_method,
            'gateway_reference' => $statusResponse['transaction_id'] ?? $payment->gateway_reference,
            'paid_at' => in_array($paymentStatus, [Payment::STATUS_SUCCESS]) ? now() : $payment->paid_at,
        ]);

        // If payment succeeded, reduce stock and remove purchased quantities from the user's cart,
        // then mark the transaction as paid. All done inside a DB transaction to keep things consistent.
        if ($payment->status === Payment::STATUS_SUCCESS) {
            if ($transaction->status !== 'paid') {
                DB::transaction(function () use ($transaction) {
                    $transaction->load('items.product');

                    foreach ($transaction->items as $item) {
                        $product = $item->product;
                        if ($product) {
                            // If stock is lower than ordered quantity, log a warning and decrement by available amount.
                            if ($product->stock < $item->quantity) {
                                \Illuminate\Support\Facades\Log::warning('Insufficient stock when finalizing paid transaction', ['product_id' => $product->id, 'transaction_id' => $transaction->id, 'stock' => $product->stock, 'required' => $item->quantity]);
                            }

                            $decrement = min($product->stock, $item->quantity);
                            if ($decrement > 0) {
                                $product->decrement('stock', $decrement);
                            }
                        }

                        // Remove purchased quantities from the user's cart (if present)
                        $cartItem = CartItem::where('user_id', $transaction->user_id)
                            ->where('product_id', $item->product_id)
                            ->first();

                        if ($cartItem) {
                            if ($cartItem->quantity > $item->quantity) {
                                $cartItem->decrement('quantity', $item->quantity);
                            } else {
                                $cartItem->delete();
                            }
                        }
                    }

                    $transaction->update(['status' => 'paid']);
                });
            }
        }

        // If payment expired/failed/cancelled, update transaction status to cancelled when appropriate
        if (in_array($payment->status, [Payment::STATUS_EXPIRED, Payment::STATUS_CANCELLED])) {
            $transaction->update(['status' => 'cancelled']);
        }

        return response()->json(['status' => 'success']);
    }
}


