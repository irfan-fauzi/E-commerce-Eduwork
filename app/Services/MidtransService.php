<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected $serverKey;
    protected $clientKey;
    protected $isProduction;
    /** @var string|null Midtrans Snap API base URL */
    protected ?string $snapApiUrl = null;

    /** @var string|null Midtrans Core API base URL */
    protected ?string $coreApiUrl = null;

    public function __construct()
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
// Set sanitization on (default)
\Midtrans\Config::$isSanitized = true;
// Set 3DS transaction for credit card to true
\Midtrans\Config::$is3ds = true;

        $this->serverKey = env('MIDTRANS_SERVER_KEY');
        $this->clientKey = env('MIDTRANS_CLIENT_KEY');
        $this->isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);

        // Set API endpoints based on environment
        $this->snapApiUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';

        $this->coreApiUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Create a Snap token for a transaction.
     * In production mode this will call Midtrans API. If Midtrans credentials are
     * missing or invalid an exception will be thrown so callers can handle it.
     *
     * @return string
     * @throws \RuntimeException|\Exception
     */
    public function createSnapToken($orderId, $totalAmount, $user, $itemDetails = null): string
    {
        // Validate keys are present and look valid
        $serverKey = (string) $this->serverKey;
        $clientKey = (string) $this->clientKey;

        if (empty($serverKey) || empty($clientKey) || str_contains($serverKey, 'YOUR_SERVER_KEY') || str_contains($clientKey, 'YOUR_CLIENT_KEY')) {
            Log::error('Midtrans keys not configured', ['order_id' => $orderId]);
            throw new \RuntimeException('Midtrans keys are not configured. Set MIDTRANS_SERVER_KEY and MIDTRANS_CLIENT_KEY in .env');
        }

        try {
            $client = new Client();

            $transactionDetails = [
                'order_id' => (string)$orderId,
                'gross_amount' => (int)$totalAmount,
            ];

            $customerDetails = [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '-',
            ];

            $payload = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails ?? [
                    [
                        'id' => $orderId,
                        'price' => (int)$totalAmount,
                        'quantity' => 1,
                        'name' => 'Order #' . $orderId,
                    ]
                ],
            ];

            $response = $client->post($this->snapApiUrl . '/transactions', [
                'auth' => [$this->serverKey, ''],
                'json' => $payload,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            $result = json_decode($response->getBody(), true);

            if (isset($result['token'])) {
                return $result['token'];
            }

            Log::error('Midtrans token creation failed', ['response' => $result, 'order_id' => $orderId]);
            throw new \Exception('Failed to create Snap token');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle 401 Unauthorized - likely invalid keys
            $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : null;
            Log::error('Midtrans client exception', ['error' => $e->getMessage(), 'order_id' => $orderId, 'status' => $status]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Midtrans service error', ['error' => $e->getMessage(), 'order_id' => $orderId]);
            throw $e;
        }
    }

    public function getTransactionStatus($orderId)
    {
        try {
            $client = new Client();

            // Use Midtrans Core API to get transaction status
            $response = $client->get($this->coreApiUrl . '/' . $orderId . '/status', [
                'auth' => [$this->serverKey, ''],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Midtrans status check error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
            throw $e;
        }
    }

    /**
     * Attempt to cancel a transaction using Midtrans Core API.
     * Returns the API response or false on failure.
     */
    public function cancelTransaction($orderId)
    {
        try {
            // Use Midtrans Core API cancel endpoint
            $client = new Client();
            $response = $client->post($this->coreApiUrl . '/' . $orderId . '/cancel', [
                'auth' => [$this->serverKey, ''],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::warning('Failed to cancel transaction via Midtrans API', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return false;
        }
    }

}
