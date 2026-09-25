<?php

use App\Livewire\Shop\CartCount;
use App\Livewire\Shop\CartPage;
use App\Models\Product;
use App\Support\Cart;
use Livewire\Livewire;

test('an empty cart says so', function () {
    $this->get(route('cart'))
        ->assertOk()
        ->assertSee('Browse products');
});

test('the cart lists its lines with a subtotal', function () {
    $serum = Product::factory()->create(['name' => 'Glow Serum', 'price' => 5000]);
    $oil = Product::factory()->create(['name' => 'Body Oil', 'price' => 2500]);

    app(Cart::class)->add($serum, 2);
    app(Cart::class)->add($oil);

    Livewire::test(CartPage::class)
        ->assertSee('Glow Serum')
        ->assertSee('Body Oil')
        ->assertSee('₦10,000')
        ->assertSee('₦12,500');
});

test('quantities can be changed and lines removed', function () {
    $product = Product::factory()->create(['price' => 1000]);
    $cart = app(Cart::class);
    $cart->add($product, 2);

    $component = Livewire::test(CartPage::class)
        ->call('increment', $product->id)
        ->assertDispatched('cart-updated');

    expect($cart->count())->toBe(3);

    $component->call('decrement', $product->id);
    expect($cart->count())->toBe(2);

    $component->call('remove', $product->id);
    expect($cart->isEmpty())->toBeTrue();
});

test('decrementing the last unit removes the line', function () {
    $product = Product::factory()->create();
    app(Cart::class)->add($product);

    Livewire::test(CartPage::class)->call('decrement', $product->id);

    expect(app(Cart::class)->isEmpty())->toBeTrue();
});

test('the cart reads current prices and drops products that are no longer for sale', function () {
    $kept = Product::factory()->create(['price' => 1000]);
    $hidden = Product::factory()->create(['price' => 1000]);
    $cart = app(Cart::class);
    $cart->add($kept);
    $cart->add($hidden);

    $kept->update(['price' => 1500]);
    $hidden->update(['is_active' => false]);

    expect($cart->lines())->toHaveCount(1)
        ->and($cart->subtotal())->toBe(1500)
        ->and($cart->count())->toBe(1);
});

test('quantities are capped', function () {
    $product = Product::factory()->create();
    $cart = app(Cart::class);

    $cart->add($product, Cart::MAX_QUANTITY + 20);

    expect($cart->count())->toBe(Cart::MAX_QUANTITY);
});

test('the header badge shows how many items are in the cart', function () {
    $product = Product::factory()->create();
    app(Cart::class)->add($product, 3);

    Livewire::test(CartCount::class)->assertSee('Cart, 3 items');
});

test('the cart never holds more than is in stock', function () {
    $product = Product::factory()->create(['stock' => 4]);
    $cart = app(Cart::class);

    expect($cart->add($product, 3))->toBe(3)
        ->and($cart->add($product, 3))->toBe(1)
        ->and($cart->count())->toBe(4);

    $cart->update($product->id, 10);
    expect($cart->count())->toBe(4);
});

test('lines shrink when stock drops and sold out products drop out', function () {
    $shrinks = Product::factory()->create(['stock' => 5]);
    $soldOut = Product::factory()->create(['stock' => 2]);
    $cart = app(Cart::class);
    $cart->add($shrinks, 5);
    $cart->add($soldOut, 2);

    $shrinks->update(['stock' => 2]);
    $soldOut->update(['stock' => 0]);

    expect($cart->lines())->toHaveCount(1)
        ->and($cart->count())->toBe(2);
});
