<?php

use App\Enums\PaymentStatus;
use App\Livewire\Admin\Dashboard;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

function paidOrder(int $subtotal, $paidAt, array $items = []): Order
{
    $order = Order::factory()->create([
        'subtotal' => $subtotal,
        'status' => 'confirmed',
        'payment_status' => PaymentStatus::Paid,
        'paid_at' => $paidAt,
    ]);

    foreach ($items as $name => $quantity) {
        $order->items()->create(['product_name' => $name, 'unit_price' => 1000, 'quantity' => $quantity, 'line_total' => 1000 * $quantity]);
    }

    return $order;
}

test('/admin shows the dashboard', function () {
    $this->get('/admin')->assertOk()->assertSee('Sales today')->assertSee('Low or out of stock');
});

test('sales add up paid orders by when they were paid', function () {
    $this->travelTo(now()->setDate(2026, 9, 24)->setTime(12, 0)); // a Thursday

    paidOrder(5000, now());
    paidOrder(3000, now()->subDays(2)); // Tuesday, same week
    paidOrder(2000, now()->subDays(20)); // 4 September, same month
    paidOrder(1000, now()->subMonth()); // August
    Order::factory()->create(['subtotal' => 99999]); // unpaid

    $sales = Livewire::test(Dashboard::class)->instance()->sales;

    expect($sales['today'])->toMatchArray(['revenue' => 5000, 'orders' => 1])
        ->and($sales['week'])->toMatchArray(['revenue' => 8000, 'orders' => 2])
        ->and($sales['month'])->toMatchArray(['revenue' => 10000, 'orders' => 3]);
});

test('the dashboard lists best sellers, low stock and recent orders', function () {
    paidOrder(10000, now(), ['Milk Tea' => 3, 'Waffle' => 1]);
    paidOrder(4000, now(), ['Waffle' => 4]);
    Product::factory()->create(['name' => 'Almost Gone Cake', 'stock' => 2, 'is_active' => true]);
    Product::factory()->create(['name' => 'Plenty Cookies', 'stock' => 50, 'is_active' => true]);
    Product::factory()->create(['name' => 'Uncounted Bread', 'stock' => null, 'is_active' => true]);
    $recent = Order::factory()->create(['customer_name' => 'Chidi Recent']);

    $component = Livewire::test(Dashboard::class)
        ->assertSee('Almost Gone Cake')
        ->assertDontSee('Plenty Cookies')
        ->assertDontSee('Uncounted Bread')
        ->assertSee($recent->reference);

    expect($component->instance()->bestSellers->pluck('product_name')->all())->toBe(['Waffle', 'Milk Tea']);
});

test('the dashboard counts failed payments and orders waiting for payment', function () {
    Order::factory()->count(2)->create();
    Payment::factory()->create(['status' => 'failed']);

    $component = Livewire::test(Dashboard::class)->instance();

    // The payment factory makes its own unpaid order too.
    expect($component->awaitingPayment)->toBe(3)
        ->and($component->failedPaymentsThisWeek)->toBe(1);
});

test('non-admins cannot see the dashboard or transactions', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/admin')->assertForbidden();
    $this->get(route('admin.transactions.index'))->assertForbidden();
});
