<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Livewire\Shop\Checkout;
use App\Livewire\Shop\OrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Payments\PaymentGateway;
use App\Support\Cart;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

function fillCheckout($component)
{
    return $component
        ->set('customer_name', 'Ada Obi')
        ->set('customer_phone', '+234 803 123 4567')
        ->set('customer_email', 'ada@example.com')
        ->set('delivery_area', 'Lagos')
        ->set('delivery_address', '12 Allen Avenue, Ikeja');
}

test('checkout sends an empty cart back to the cart page', function () {
    $this->get(route('checkout'))->assertRedirect(route('cart'));
});

test('placing an order saves it with its items and empties the cart', function () {
    $serum = Product::factory()->create(['name' => 'Glow Serum', 'price' => 5000]);
    $oil = Product::factory()->create(['name' => 'Body Oil', 'price' => 2500]);
    app(Cart::class)->add($serum, 2);
    app(Cart::class)->add($oil);

    $component = fillCheckout(Livewire::test(Checkout::class))
        ->set('notes', 'Call before delivery')
        ->call('placeOrder')
        ->assertHasNoErrors();

    $order = Order::sole();
    $component->assertRedirect(route('orders.show', $order));

    expect($order->status)->toBe(OrderStatus::Pending)
        ->and($order->reference)->toStartWith('MW-')
        ->and($order->customer_name)->toBe('Ada Obi')
        ->and($order->customer_email)->toBe('ada@example.com')
        ->and($order->notes)->toBe('Call before delivery')
        ->and($order->subtotal)->toBe(12500)
        ->and($order->items)->toHaveCount(2)
        ->and($order->items->firstWhere('product_name', 'Glow Serum')->line_total)->toBe(10000)
        ->and(app(Cart::class)->isEmpty())->toBeTrue();
});

test('order items keep the price paid when the product changes later', function () {
    $product = Product::factory()->create(['name' => 'Glow Serum', 'price' => 5000]);
    app(Cart::class)->add($product);

    fillCheckout(Livewire::test(Checkout::class))->call('placeOrder');

    $product->update(['name' => 'Glow Serum v2', 'price' => 9000]);

    $item = Order::sole()->items->sole();
    expect($item->product_name)->toBe('Glow Serum')
        ->and($item->unit_price)->toBe(5000);
});

test('checkout validates the delivery details', function () {
    app(Cart::class)->add(Product::factory()->create());

    Livewire::test(Checkout::class)
        ->set('customer_phone', 'call me')
        ->set('customer_email', 'not-an-email')
        ->set('delivery_area', 'Mars')
        ->call('placeOrder')
        ->assertHasErrors(['customer_name', 'customer_phone', 'customer_email', 'delivery_area', 'delivery_address']);

    expect(Order::count())->toBe(0);
});

test('an order cannot be placed once the cart has emptied', function () {
    $product = Product::factory()->create();
    app(Cart::class)->add($product);

    $component = fillCheckout(Livewire::test(Checkout::class));

    app(Cart::class)->clear();

    $component->call('placeOrder')->assertHasErrors('cart');

    expect(Order::count())->toBe(0);
});

test('a signed in customer has their details filled in and the order linked', function () {
    $user = User::factory()->create(['name' => 'Ada Obi', 'email' => 'ada@example.com']);
    app(Cart::class)->add(Product::factory()->create());

    $this->actingAs($user);

    Livewire::test(Checkout::class)
        ->assertSet('customer_name', 'Ada Obi')
        ->assertSet('customer_email', 'ada@example.com')
        ->set('customer_phone', '08031234567')
        ->set('delivery_area', 'Abuja')
        ->set('delivery_address', 'Wuse 2')
        ->call('placeOrder')
        ->assertHasNoErrors();

    expect(Order::sole()->user_id)->toBe($user->id);
});

test('the confirmation page shows the order to the browser that placed it', function () {
    $order = Order::factory()->create(['subtotal' => 7500]);
    $order->items()->create(['product_name' => 'Glow Serum', 'unit_price' => 2500, 'quantity' => 3, 'line_total' => 7500]);

    $this->withSession([Checkout::PLACED_ORDERS_SESSION_KEY => [$order->reference]])
        ->get(route('orders.show', $order))
        ->assertOk()
        ->assertSee($order->reference)
        ->assertSee('3 &times; Glow Serum', escape: false)
        ->assertSee('₦7,500')
        ->assertSee('Pay ₦7,500')
        ->assertSee('Online payment is being set up')
        ->assertDontSee('Send order on WhatsApp');
});

test('the confirmation page is hidden from anyone else', function () {
    $order = Order::factory()->create();

    $this->get(route('orders.show', $order))->assertNotFound();
});

test('a signed in customer can reopen their own order', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->get(route('orders.show', $order))->assertOk();
});

test('an order starts unpaid', function () {
    app(Cart::class)->add(Product::factory()->create());

    fillCheckout(Livewire::test(Checkout::class))->call('placeOrder');

    expect(Order::sole()->payment_status)->toBe(PaymentStatus::Unpaid);
});

test('placing an order takes the units out of stock', function () {
    $tracked = Product::factory()->create(['stock' => 5]);
    $untracked = Product::factory()->create(['stock' => null]);
    app(Cart::class)->add($tracked, 3);
    app(Cart::class)->add($untracked, 2);

    fillCheckout(Livewire::test(Checkout::class))->call('placeOrder')->assertHasNoErrors();

    expect($tracked->fresh()->stock)->toBe(2)
        ->and($untracked->fresh()->stock)->toBeNull();
});

test('an order is refused when stock ran out after it was carted', function () {
    $product = Product::factory()->create(['stock' => 3]);
    app(Cart::class)->add($product, 3);

    $component = fillCheckout(Livewire::test(Checkout::class));

    // Someone else bought two while this shopper was filling in the form.
    DB::table('products')->where('id', $product->id)->update(['stock' => 1]);

    $component->call('placeOrder')->assertHasErrors('cart');

    expect(Order::count())->toBe(0)
        ->and($product->fresh()->stock)->toBe(1)
        ->and(app(Cart::class)->count())->toBe(1);
});

test('the pay button hands the order to the payment gateway when one is set up', function () {
    config(['milkyway.shop.payment_gateway' => 'fake']);
    app()->instance(PaymentGateway::class, new class implements PaymentGateway
    {
        public function checkoutUrl(Order $order): string
        {
            return 'https://pay.example/'.$order->reference;
        }
    });

    $order = Order::factory()->create(['subtotal' => 5000]);
    session()->put(Checkout::PLACED_ORDERS_SESSION_KEY, [$order->reference]);

    Livewire::test(OrderConfirmation::class, ['order' => $order])
        ->assertSee('Pay ₦5,000')
        ->call('pay')
        ->assertRedirect('https://pay.example/'.$order->reference);
});
