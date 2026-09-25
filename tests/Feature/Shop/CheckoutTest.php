<?php

use App\Enums\OrderStatus;
use App\Livewire\Shop\Checkout;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\Cart;
use Livewire\Livewire;

function fillCheckout($component)
{
    return $component
        ->set('customer_name', 'Ada Obi')
        ->set('customer_phone', '+234 803 123 4567')
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
        ->and($order->customer_email)->toBeNull()
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
        ->assertSee('https://wa.me/'.config('milkyway.whatsapp.number'), escape: false);
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
