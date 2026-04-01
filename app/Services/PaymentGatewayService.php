<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


/**
 * Service untuk integrasi Payment Gateway Pakasir
 * URL: https://app.pakasir.com/pay/{slug}/{amount}?order_id={order_id}&qris_only=1
 */
class PaymentGatewayService
{
    private string $baseUrl = 'https://app.pakasir.com';
    private string $slug = 'parqeer-app';
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.pakasir.api_key');
    }

    /**
     * Generate payment URL untuk Pakasir
     */
    public function generatePaymentUrl(Transaction $transaction): string
    {
        // Jika amount belum diisi, hitung dulu
        if ($transaction->amount <= 0) {
            $calculation = $transaction->calculatePayment();
            $amount = (int) $calculation['amount'];

            $transaction->update([
                'amount' => $amount
            ]);
        } else {
            $amount = (int) $transaction->amount;
        }
        $orderId = $transaction->order_id ?? 'TRX' . $transaction->id . '-' . time();

        // Simpan order_id jika belum ada
        if (!$transaction->order_id) {
            $transaction->update(['order_id' => $orderId]);
        }

        $slug = 'parqeer-app'; // ganti sesuai proyek Pakasir kamu
        $url = "https://app.pakasir.com/pay/{$slug}/{$amount}?order_id={$orderId}&qris_only=1";

        Log::info('Generated Pakasir payment URL', [
            'transaction_id' => $transaction->id,
            'order_id' => $orderId,
            'amount' => $amount,
            'url' => $url
        ]);

        return $url;
    }

    /**
     * Handle callback dari Pakasir setelah pembayaran berhasil
     */
    public function handleCallback(Request $request): array
    {
        $project = $request->input('project');
        $orderId = $request->input('order_id');
        $amount = $request->input('amount');
        $status = $request->input('status');
        $paymentMethod = $request->input('payment_method');

        Log::info('Pakasir webhook received', [
            'project' => $project,
            'order_id' => $orderId,
            'amount' => $amount,
            'status' => $status,
            'payment_method' => $paymentMethod,
        ]);

        // Validasi order_id/amount
        $transaction = Transaction::where('order_id', $orderId)->first();
        if (!$transaction) {
            Log::error('Transaction not found for order_id', ['order_id' => $orderId]);
            return ['success' => false, 'message' => 'Transaction not found'];
        }

        if ((float) $transaction->amount !== (float) $amount) {
            Log::error('Amount mismatch in webhook', ['order_id' => $orderId, 'transaction_amount' => $transaction->amount, 'webhook_amount' => $amount]);
            return ['success' => false, 'message' => 'Amount mismatch test', 'transaction_amount' => $transaction->amount, 'webhook_amount' => $amount];
        }

        if ($status !== 'completed') {
            Log::warning('Webhook status not completed', ['order_id' => $orderId, 'status' => $status]);
            return ['success' => false, 'message' => 'Payment not completed'];
        }

        // Pastikan status transaksi belum dibayar
        if ($transaction->status !== 'in') {
            Log::info('Transaction already processed', ['order_id' => $orderId, 'status' => $transaction->status]);
            return ['success' => true, 'message' => 'Already processed'];
        }

        // Set paid via QRIS
        try {
            $transaction->processPayment((float) $amount, now(), $paymentMethod ?? 'qris', [
                'gateway' => 'pakasir',
                'webhook' => $request->all(),
            ]);

            Log::info('Transaction marked as paid via webhook', ['order_id' => $orderId]);

            return ['success' => true, 'transaction_id' => $transaction->id, 'message' => 'Payment processed'];
        } catch (\Exception $e) {
            Log::error('Failed to process payment callback', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Failed to process payment'];
        }
    }

    /**
     * Extract transaction ID dari order_id
     * Format: TRX-{id}-{timestamp}
     */
    private function extractTransactionId(string $orderId): ?int
    {
        $parts = explode('-', $orderId);

        if (count($parts) >= 2 && $parts[0] === 'TRX') {
            return (int) $parts[1];
        }

        return null;
    }

    /**
     * Call Pakasir payment simulation endpoint
     */
    private function simulatePayment(string $project, string $orderId, int $amount, string $apiKey): array
    {
        try {
            $promise = Http::async()->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://app.pakasir.com/api/paymentsimulation', [
                'project' => $project,
                'order_id' => $orderId,
                'amount' => $amount,
                'api_key' => $apiKey
            ]);
            $response = $promise->wait();

            Log::info('Payment simulation response', [
                'order_id' => $orderId,
                'status_code' => $response->status(),
                'response_body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Check if simulation was successful
                if (isset($data['status']) && $data['status'] === 'success') {
                    return [
                        'success' => true,
                        'data' => $data,
                        'message' => 'Payment simulation successful'
                    ];
                } else {
                    return [
                        'success' => false,
                        'data' => $data,
                        'message' => $data['message'] ?? 'Payment simulation failed'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Payment simulation API error: ' . $response->status(),
                    'response' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Payment simulation exception', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Payment simulation exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check payment status dari Pakasir API
     */
    public function checkPaymentStatus(string $orderId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/api/payment/status/' . $orderId);

            dd(get_class($response));

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['status'] ?? 'unknown',
                    'amount' => $data['amount'] ?? 0,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to check payment status',
                'response' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Error checking payment status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
