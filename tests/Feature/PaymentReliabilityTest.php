<?php

use App\Actions\Shop\RecordPayment;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Livewire\Admin\Orders\Show;
use App\Livewire\Shop\Checkout;
use App\Livewire\Shop\OrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    config([
        'services.paystack.secret_key' => 'sk_test_secret',
        'services.paystack.base_url' => 'https://api.paystack.test',
        'milkyway.shop.payment_gateway' => 'paystack',
    ]);
    Http::preventStrayRequests();
});

test('cancelling an order releases tracked stock only once', function () {
    $this->actingAs(User::factory()->admin()->create());
    $order = Order::factory()->create();
    $product = Product::factory()->create(['stock' => 2]);
    $untracked = Product::factory()->create(['stock' => null]);
    foreach ([$product, $untracked] as $item) {
        $order->items()->create(['product_id' => $item->id, 'product_name' => $item->name, 'unit_price' => 5000, 'quantity' => 3, 'line_total' => 15000]);
    }

    Livewire::test(Show::class, ['order' => $order])
        ->set('status', 'cancelled')->call('updateStatus')->assertHasNoErrors()
        ->call('updateStatus')->assertHasNoErrors();

    expect($order->fresh()->status)->toBe(OrderStatus::Cancelled);
    expect($product->fresh()->stock)->toBe(5);
    expect($untracked->fresh()->stock)->toBeNull();
});

test('cancelled orders cannot be reopened by admins', function () {
    $this->actingAs(User::factory()->admin()->create());
    $order = Order::factory()->create(['status' => OrderStatus::Cancelled]);

    Livewire::test(Show::class, ['order' => $order])
        ->set('status', 'confirmed')->call('updateStatus')->assertHasErrors('status');

    expect($order->fresh()->status)->toBe(OrderStatus::Cancelled);
});

test('unpaid pending orders expire after 24 hours without cancelling paid or active orders', function () {
    $this->freezeTime();
    $product = Product::factory()->create(['stock' => 1]);
    $expired = Order::factory()->create(['created_at' => now()->subDay()]);
    $expired->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'unit_price' => 1000, 'quantity' => 2, 'line_total' => 2000]);
    $recent = Order::factory()->create(['created_at' => now()->subHours(23)]);
    $paid = Order::factory()->create(['created_at' => now()->subDays(2), 'payment_status' => PaymentStatus::Paid]);
    $confirmed = Order::factory()->create(['created_at' => now()->subDays(2), 'status' => OrderStatus::Confirmed]);

    $this->artisan('app:expire-unpaid-orders')->assertSuccessful();
    $this->artisan('app:expire-unpaid-orders')->assertSuccessful();

    expect($expired->fresh()->status)->toBe(OrderStatus::Cancelled);
    expect($product->fresh()->stock)->toBe(3);
    expect($recent->fresh()->status)->toBe(OrderStatus::Pending);
    expect($paid->fresh()->status)->toBe(OrderStatus::Pending);
    expect($confirmed->fresh()->status)->toBe(OrderStatus::Confirmed);
});

test('a failure using an old order snapshot cannot overwrite a successful payment', function (bool $sameAttempt) {
    $order = Order::factory()->create(['subtotal' => 5000]);
    $stale = $order->fresh();
    $recorder = app(RecordPayment::class);
    $recorder->succeeded($order, 'paid-attempt', 500000);

    $recorder->failed($stale, $sameAttempt ? 'paid-attempt' : 'failed-attempt', 'Declined');

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Paid);
    expect($order->fresh()->payment_reference)->toBe('paid-attempt');
    expect($order->payments()->where('reference', 'paid-attempt')->sole()->status)->toBe(TransactionStatus::Successful);
})->with(['same attempt' => true, 'another attempt' => false]);

test('an abandoned response cannot overwrite a successful payment', function () {
    $order = Order::factory()->create(['subtotal' => 5000]);
    $recorder = app(RecordPayment::class);
    $recorder->succeeded($order, 'paid-attempt', 500000);

    $recorder->abandoned($order, 'paid-attempt');

    expect($order->payments()->sole()->status)->toBe(TransactionStatus::Successful);
});

test('cancelled orders cannot start payment even from an already open page', function () {
    Http::fake();
    $order = Order::factory()->create();
    session()->put(Checkout::PLACED_ORDERS_SESSION_KEY, [$order->reference]);
    $component = Livewire::test(OrderConfirmation::class, ['order' => $order]);
    $order->update(['status' => OrderStatus::Cancelled]);

    $component->call('pay')->assertNoRedirect()->assertDontSee('wire:click="pay"', escape: false)->assertSee('This order has been cancelled');

    Http::assertNothingSent();
});

test('late payments are recorded without reopening cancelled orders or resetting delivery progress', function (OrderStatus $status) {
    $order = Order::factory()->create(['subtotal' => 5000, 'status' => $status, 'payment_reference' => 'late-attempt']);
    Http::fake(['api.paystack.test/transaction/verify/late-attempt' => Http::response(['data' => ['status' => 'success', 'amount' => 500000]])]);

    $this->get(route('payments.paystack.callback', ['reference' => 'late-attempt']))->assertRedirect(route('orders.show', $order));

    expect($order->fresh()->status)->toBe($status);
    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Paid);
    expect($order->payments()->sole()->status)->toBe(TransactionStatus::Successful);
})->with([OrderStatus::Cancelled, OrderStatus::Dispatched, OrderStatus::Delivered]);

test('pending payments tell the shopper to wait instead of claiming failure', function () {
    $order = Order::factory()->create(['payment_reference' => 'pending-attempt']);
    Http::fake(['api.paystack.test/transaction/verify/pending-attempt' => Http::response(['data' => ['status' => 'pending', 'amount' => 500000]])]);

    $this->get(route('payments.paystack.callback', ['reference' => 'pending-attempt']))
        ->assertSessionHas('payment_notice', 'We could not confirm your payment yet. If you were charged, it will show here shortly.');

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Unpaid);
});

test('a verification connection failure gives the shopper an unconfirmed notice', function () {
    $order = Order::factory()->create(['payment_reference' => 'pending-attempt']);
    Http::fake(['api.paystack.test/transaction/verify/pending-attempt' => Http::failedConnection()]);

    $this->get(route('payments.paystack.callback', ['reference' => 'pending-attempt']))
        ->assertRedirect(route('orders.show', $order))->assertSessionHas('payment_notice');

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Unpaid);
});

test('admins can recover payment confirmation for an older attempt', function () {
    $this->actingAs(User::factory()->admin()->create());
    $order = Order::factory()->create(['subtotal' => 5000, 'payment_reference' => 'new-attempt']);
    $payment = $order->payments()->create(['provider' => 'paystack', 'reference' => 'old-attempt', 'amount' => 5000]);
    Http::fake(['api.paystack.test/transaction/verify/old-attempt' => Http::response(['data' => ['status' => 'success', 'amount' => 500000]])]);

    Livewire::test(Show::class, ['order' => $order])->assertSee('Check payment')
        ->call('checkPayment', $payment->id)->assertSee('Payment confirmed.');

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Paid);
    expect($payment->fresh()->status)->toBe(TransactionStatus::Successful);
});

test('admin payment checks handle unavailable paystack without changing payment records', function () {
    $this->actingAs(User::factory()->admin()->create());
    $order = Order::factory()->create();
    $payment = $order->payments()->create(['provider' => 'paystack', 'reference' => 'pending-attempt', 'amount' => 5000]);
    Http::fake(['api.paystack.test/transaction/verify/pending-attempt' => Http::failedConnection()]);

    Livewire::test(Show::class, ['order' => $order])->call('checkPayment', $payment->id)->assertSee('Paystack is unavailable.');

    expect($payment->fresh()->status)->toBe(TransactionStatus::Pending);
    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Unpaid);
});

test('admin payment checks cannot use a payment from another order', function () {
    $this->actingAs(User::factory()->admin()->create());
    $order = Order::factory()->create();
    $other = Order::factory()->create();
    $payment = $other->payments()->create(['provider' => 'paystack', 'reference' => 'other-attempt', 'amount' => 5000]);
    Http::fake();

    Livewire::test(Show::class, ['order' => $order])->call('checkPayment', $payment->id)->assertNotFound();

    Http::assertNothingSent();
});

test('non admins cannot check payments', function () {
    $this->actingAs(User::factory()->create());
    $order = Order::factory()->create();
    $payment = $order->payments()->create(['provider' => 'paystack', 'reference' => 'attempt', 'amount' => 5000]);
    Http::fake();

    Livewire::test(Show::class, ['order' => $order])->call('checkPayment', $payment->id)->assertForbidden();

    Http::assertNothingSent();
});

test('admin payment checks report pending failed abandoned and insufficient payments accurately', function (string $status, int $amount, PaymentStatus $expected, string $notice) {
    $this->actingAs(User::factory()->admin()->create());
    $order = Order::factory()->create(['subtotal' => 5000]);
    $payment = $order->payments()->create(['provider' => 'paystack', 'reference' => 'check-attempt', 'amount' => 5000]);
    Http::fake(['api.paystack.test/transaction/verify/check-attempt' => Http::response(['data' => ['status' => $status, 'amount' => $amount]])]);

    Livewire::test(Show::class, ['order' => $order])->call('checkPayment', $payment->id)->assertSee($notice);

    expect($order->fresh()->payment_status)->toBe($expected);
})->with([
    'pending' => ['pending', 500000, PaymentStatus::Unpaid, 'Payment is still awaiting confirmation.'],
    'failed' => ['failed', 500000, PaymentStatus::Failed, 'Paystack reports this attempt failed.'],
    'abandoned' => ['abandoned', 500000, PaymentStatus::Unpaid, 'Paystack reports this attempt was abandoned.'],
    'underpaid' => ['success', 100, PaymentStatus::Failed, 'Payment amount does not cover the order.'],
]);

test('a cancelled paid order tells the customer to arrange a refund', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Cancelled, 'payment_status' => PaymentStatus::Paid]);
    session()->put(Checkout::PLACED_ORDERS_SESSION_KEY, [$order->reference]);

    $this->get(route('orders.show', $order))->assertSee('please contact us to arrange a refund.')
        ->assertDontSee('wire:click="pay"', escape: false);
});

test('a payment connection failure leaves the order available to retry', function () {
    $order = Order::factory()->create();
    session()->put(Checkout::PLACED_ORDERS_SESSION_KEY, [$order->reference]);
    Http::fake(['api.paystack.test/transaction/initialize' => Http::failedConnection()]);

    Livewire::test(OrderConfirmation::class, ['order' => $order])->call('pay')
        ->assertHasErrors('payment')->assertNoRedirect();

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Unpaid);
    $this->assertDatabaseCount('payments', 0);
});
