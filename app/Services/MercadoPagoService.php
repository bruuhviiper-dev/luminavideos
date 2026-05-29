<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\ChannelMembership;
use App\Models\SuperChat;
use App\Models\CreatorEarning;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    private string $accessToken;
    private string $publicKey;

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token', '');
        $this->publicKey = config('services.mercadopago.public_key', '');
    }

    /**
     * Create a recurring subscription for channel membership.
     */
    public function createSubscription(array $data): array
    {
        if (empty($this->accessToken)) {
            return ['success' => false, 'error' => 'Mercado Pago not configured'];
        }

        try {
            $payload = [
                'reason' => $data['plan_name'] ?? 'Assinatura do canal',
                'auto_recurring' => [
                    'frequency' => 1,
                    'frequency_type' => 'months',
                    'transaction_amount' => $data['amount'],
                    'currency_id' => 'BRL',
                ],
                'payer_email' => $data['payer_email'],
                'back_url' => url('/studio/monetizacao'),
                'status' => 'authorized',
            ];

            $response = $this->makeRequest('POST', 'https://api.mercadopago.com/preapproval', $payload);

            if (isset($response['id'])) {
                return ['success' => true, 'subscription_id' => $response['id'], 'init_point' => $response['init_point'] ?? null];
            }

            return ['success' => false, 'error' => $response['message'] ?? 'Unknown error'];
        } catch (\Exception $e) {
            Log::error('MercadoPago createSubscription error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Cancel an existing subscription.
     */
    public function cancelSubscription(string $subscriptionId): array
    {
        try {
            $response = $this->makeRequest('PUT', "https://api.mercadopago.com/preapproval/{$subscriptionId}", [
                'status' => 'cancelled',
            ]);

            return ['success' => ($response['status'] ?? '') === 'cancelled'];
        } catch (\Exception $e) {
            Log::error('MercadoPago cancelSubscription error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create a one-time payment (Super Chat, etc.).
     */
    public function createPayment(array $data): array
    {
        if (empty($this->accessToken)) {
            return ['success' => false, 'error' => 'Mercado Pago not configured'];
        }

        try {
            $payload = [
                'transaction_amount' => (float) $data['amount'],
                'token' => $data['token'] ?? null,
                'description' => $data['description'] ?? 'Super Chat Tubiii',
                'installments' => 1,
                'payment_method_id' => $data['payment_method_id'] ?? 'pix',
                'payer' => [
                    'email' => $data['payer_email'],
                ],
            ];

            $response = $this->makeRequest('POST', 'https://api.mercadopago.com/v1/payments', $payload);

            if (isset($response['id'])) {
                return [
                    'success' => true,
                    'payment_id' => $response['id'],
                    'status' => $response['status'],
                    'point_of_interaction' => $response['point_of_interaction'] ?? null,
                ];
            }

            return ['success' => false, 'error' => $response['message'] ?? 'Payment failed'];
        } catch (\Exception $e) {
            Log::error('MercadoPago createPayment error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Handle webhook notifications from Mercado Pago.
     */
    public function handleWebhook(array $payload): bool
    {
        try {
            $type = $payload['type'] ?? '';
            $dataId = $payload['data']['id'] ?? null;

            if (!$dataId) return false;

            match ($type) {
                'payment' => $this->processPaymentWebhook($dataId),
                'subscription_preapproval' => $this->processSubscriptionWebhook($dataId),
                default => null,
            };

            return true;
        } catch (\Exception $e) {
            Log::error('MercadoPago webhook error: ' . $e->getMessage());
            return false;
        }
    }

    private function processPaymentWebhook(string $paymentId): void
    {
        $payment = Payment::where('gateway_id', $paymentId)->first();
        if (!$payment) return;

        $response = $this->makeRequest('GET', "https://api.mercadopago.com/v1/payments/{$paymentId}", []);
        $status = $response['status'] ?? 'pending';

        $payment->update(['status' => $status]);

        if ($status === 'approved') {
            CreatorEarning::create([
                'user_id' => $payment->metadata['channel_id'] ?? $payment->user_id,
                'amount' => $payment->amount * 0.7, // 70% for creator
                'type' => $payment->type,
                'description' => "Pagamento aprovado #{$paymentId}",
                'payment_id' => $payment->id,
            ]);
        }
    }

    private function processSubscriptionWebhook(string $subscriptionId): void
    {
        $membership = ChannelMembership::where('gateway_subscription_id', $subscriptionId)->first();
        if (!$membership) return;

        $response = $this->makeRequest('GET', "https://api.mercadopago.com/preapproval/{$subscriptionId}", []);
        $status = $response['status'] ?? 'pending';

        $membership->update([
            'status' => match ($status) {
                'authorized' => 'active',
                'cancelled'  => 'cancelled',
                'paused'     => 'cancelled',
                default      => 'active',
            },
        ]);
    }

    private function makeRequest(string $method, string $url, array $payload): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
            'X-Idempotency-Key: ' . uniqid('tubiii_', true),
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
