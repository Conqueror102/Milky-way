<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Livewire\Shop\Checkout;
use App\Livewire\Shop\OrderConfirmation;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    config([
        'services.paystack.secret_key' => 'sk_test_secret',
        'services.paystack.base_url' => 'https://api.paystack.test',
        'milkyway.shop.payment_gateway' => 'paystack',
    ]);
});

function paystackOrder(array $attributes = []): Order
{
    $order = Order::factory()->create(['subtotal' => 12500, 'customer_email' => 'ada@example.com', ...$attributes]);
    session()->push(Checkout::PLACED_ORDERS_SESSION_KEY, $order->reference);

    return $order;
}

function verifyResponse(string $status, int $amount): array
{
    return ['status' => true, 'data' => ['status' => $status, 'amount' => $amount]];
}

test('paying starts a paystack transaction for the order total in kobo', function () {
    Http::fake([
        'api.paystack.test/transaction/initialize' => Http::response(['status' => true, 'data' => ['authorization_url' => 'https://checkout.paystack.com/abc']]),
    ]);
    $order = paystackOrder();

    Livewire::test(OrderConfirmation::class, ['order' => $order])
        ->call('pay')
        ->assertRedirect('https://checkout.paystack.com/abc');

    Http::assertSent(fn ($request) => $request->url() === 'https://api.paystack.test/transaction/initialize'
        && $request->hasHeader('Authorization', 'Bearer sk_test_secret')
        && $request['amount'] === 1250000
        && $request['currency'] === 'NGN'
        && $request['email'] === 'ada@example.com'
        && $request['callback_url'] === route('payments.paystack.callback')
        && str_starts_with($request['reference'], $order->reference.'-'));

    expect($order->fresh()->payment_provider)->toBe('paystack')
        ->and($order->fresh()->payment_reference)->toStartWith($order->reference.'-');
});

test('a paystack error shows a message instead of breaking the page', function () {
    Http::fake(['api.paystack.test/*' => Http::response(['status' => false, 'message' => 'Invalid key'], 401)]);

    Livewire::test(OrderConfirmation::class, ['order' => paystackOrder()])
        ->call('pay')
        ->assertHasErrors('payment')
        ->assertNoRedirect();
});

test('the callback marks the order paid after verifying with paystack', function () {
    $order = paystackOrder(['payment_reference' => 'MW-ABC123-XYZ']);
    Http::fake(['api.paystack.test/transaction/verify/MW-ABC123-XYZ' => Http::response(verifyResponse('success', 1250000))]);

    $this->get(route('payments.paystack.callback', ['reference' => 'MW-ABC123-XYZ']))
        ->assertRedirect(route('orders.show', $order));

    $order->refresh();
    expect($order->payment_status)->toBe(PaymentStatus::Paid)
        ->and($order->status)->toBe(OrderStatus::Confirmed)
        ->and($order->paid_at)->not->toBeNull();
});

test('the callback does not trust a short payment', function () {
    $order = paystackOrder(['payment_reference' => 'MW-ABC123-XYZ']);
    Http::fake(['api.paystack.test/transaction/verify/*' => Http::response(verifyResponse('success', 100))]);

    $this->get(route('payments.paystack.callback', ['reference' => 'MW-ABC123-XYZ']));

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Failed);
});

test('a failed payment can be retried', function () {
    $order = paystackOrder(['payment_reference' => 'MW-ABC123-XYZ']);
    Http::fake(['api.paystack.test/transaction/verify/*' => Http::response(verifyResponse('failed', 1250000))]);

    $this->get(route('payments.paystack.callback', ['reference' => 'MW-ABC123-XYZ']))
        ->assertSessionHas('payment_notice');

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Failed);

    $this->get(route('orders.show', $order))->assertSee('Pay ₦12,500');
});

test('the callback ignores unknown references', function () {
    $this->get(route('payments.paystack.callback', ['reference' => 'nope']))->assertNotFound();
});

test('a signed webhook marks the order paid', function () {
    $order = paystackOrder(['payment_reference' => 'MW-ABC123-XYZ']);
    $payload = json_encode(['event' => 'charge.success', 'data' => ['reference' => 'MW-ABC123-XYZ', 'amount' => 1250000]]);

    $this->call('POST', route('payments.paystack.webhook'), [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac('sha512', $payload, 'sk_test_secret'),
    ], $payload)->assertOk();

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Paid);
});

test('an unsigned webhook is rejected', function () {
    $order = paystackOrder(['payment_reference' => 'MW-ABC123-XYZ']);
    $payload = json_encode(['event' => 'charge.success', 'data' => ['reference' => 'MW-ABC123-XYZ', 'amount' => 1250000]]);

    $this->call('POST', route('payments.paystack.webhook'), [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_PAYSTACK_SIGNATURE' => 'forged',
    ], $payload)->assertUnauthorized();

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Unpaid);
});

test('a payment reported twice is recorded once', function () {
    $order = paystackOrder(['payment_reference' => 'MW-ABC123-XYZ']);
    Http::fake(['api.paystack.test/transaction/verify/*' => Http::response(verifyResponse('success', 1250000))]);

    $this->get(route('payments.paystack.callback', ['reference' => 'MW-ABC123-XYZ']));
    $paidAt = $order->fresh()->paid_at;

    $this->travel(5)->minutes();
    $payload = json_encode(['event' => 'charge.success', 'data' => ['reference' => 'MW-ABC123-XYZ', 'amount' => 1250000]]);
    $this->call('POST', route('payments.paystack.webhook'), [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac('sha512', $payload, 'sk_test_secret'),
    ], $payload)->assertOk();

    expect($order->fresh()->paid_at->equalTo($paidAt))->toBeTrue();
});
