<?php

namespace App\Payments;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Paystack's hosted checkout: https://paystack.com/docs/payments/accept-payments
 */
class PaystackGateway implements PaymentGateway
{
    public function __construct(
        private string $secretKey,
        private string $baseUrl,
    ) {}

    /**
     * Start a transaction for the order and return Paystack's payment page. Each attempt
     * gets its own reference (Paystack refuses a reused one), saved on the order so the
     * callback and webhook can find it.
     */
    public function checkoutUrl(Order $order): string
    {
        $reference = $order->reference.'-'.Str::upper(Str::random(6));

        $response = $this->client()->post('/transaction/initialize', [
            'email' => $order->customer_email,
            'amount' => $order->subtotal * 100,
            'currency' => 'NGN',
            'reference' => $reference,
            'callback_url' => route('payments.paystack.callback'),
            'metadata' => ['order_reference' => $order->reference],
        ]);

        $url = $response->json('data.authorization_url');

        if ($response->failed() || ! is_string($url)) {
            throw new RuntimeException('Paystack could not start the payment: '.$response->json('message', $response->status()));
        }

        $order->update(['payment_provider' => 'paystack', 'payment_reference' => $reference]);

        return $url;
    }

    /**
     * Ask Paystack what happened to a transaction.
     *
     * @return array{status: string, amount: int, reference: string}|null
     */
    public function verify(string $reference): ?array
    {
        $response = $this->client()->get('/transaction/verify/'.rawurlencode($reference));

        $status = $response->json('data.status');
        $amount = $response->json('data.amount');

        if ($response->failed() || ! is_string($status) || ! is_numeric($amount)) {
            return null;
        }

        return ['status' => $status, 'amount' => (int) $amount, 'reference' => $reference];
    }

    /**
     * Whether a webhook body really came from Paystack: its x-paystack-signature header is
     * the body's HMAC-SHA512 under the secret key.
     */
    public function hasValidSignature(string $payload, ?string $signature): bool
    {
        return is_string($signature)
            && hash_equals(hash_hmac('sha512', $payload, $this->secretKey), $signature);
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->secretKey)
            ->acceptJson()
            ->timeout(15);
    }
}
