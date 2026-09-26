<?php

use App\Livewire\Shop\CardAddToCart;
use App\Models\Product;
use App\Support\Cart;
use Livewire\Livewire;

test('product cards on the home page have a round add to cart button', function () {
    $product = Product::factory()->create(['name' => 'Glow Serum', 'price' => 5000]);

    $this->get(route('home'))
        ->assertSeeLivewire(CardAddToCart::class)
        ->assertSee('Add Glow Serum to cart');
});

test('products that cannot be ordered get no card button', function () {
    Product::factory()->create(['name' => 'Glow Serum', 'stock' => 0]);
    Product::factory()->priceOnRequest()->create(['name' => 'Body Oil']);
    config(['milkyway.shop.demo_prices' => false]);

    $this->get(route('home'))
        ->assertDontSee('Add Glow Serum to cart')
        ->assertDontSee('Add Body Oil to cart');
});

test('the card button adds one to the cart and updates the badge', function () {
    $product = Product::factory()->create(['price' => 5000]);

    Livewire::test(CardAddToCart::class, ['product' => $product])
        ->call('add')
        ->assertSet('added', true)
        ->assertDispatched('cart-updated')
        ->call('add');

    expect(app(Cart::class)->quantityOf($product->id))->toBe(2);
});

test('the card button stops at the stock limit', function () {
    $product = Product::factory()->create(['price' => 5000, 'stock' => 1]);

    Livewire::test(CardAddToCart::class, ['product' => $product])
        ->call('add')
        ->call('add')
        ->assertSet('added', false)
        ->assertSet('problem', 'All in stock are in your cart');

    expect(app(Cart::class)->quantityOf($product->id))->toBe(1);
});
