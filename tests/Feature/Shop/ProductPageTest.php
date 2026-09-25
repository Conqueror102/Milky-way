<?php

use App\Livewire\Shop\ProductPage;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\Cart;
use Livewire\Livewire;

test('a product has its own page', function () {
    $product = Product::factory()->create(['name' => 'Glow Serum', 'price' => 12500]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Glow Serum')
        ->assertSee('₦12,500')
        ->assertSee('Add to cart');
});

test('hidden products are not found', function () {
    $product = Product::factory()->inactive()->create();

    $this->get(route('products.show', $product))->assertNotFound();
});

test('a product without a price is enquiry only', function () {
    $product = Product::factory()->priceOnRequest()->create();

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Price on request')
        ->assertDontSee('Add to cart');
});

test('a hosted image is preferred over the bundled one', function () {
    $product = Product::factory()->create([
        'category' => 'Test Category',
        'image_url' => 'https://res.cloudinary.com/demo/image/upload/serum.jpg',
        'image_path' => '/images/categories/health/anua',
    ]);

    $this->get(route('products.show', $product))
        ->assertSee('https://res.cloudinary.com/demo/image/upload/serum.jpg')
        ->assertDontSee('/images/categories/health/anua.webp');
});

test('the chosen quantity is added to the cart', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductPage::class, ['product' => $product])
        ->call('increment')
        ->call('increment')
        ->call('addToCart')
        ->assertSet('added', true)
        ->assertSet('quantity', 1)
        ->assertDispatched('cart-updated');

    expect(app(Cart::class)->count())->toBe(3);
});

test('a product that lost its price cannot be added', function () {
    $product = Product::factory()->create();

    $component = Livewire::test(ProductPage::class, ['product' => $product]);

    $product->update(['price' => null]);

    $component->call('addToCart')->assertHasErrors('quantity');

    expect(app(Cart::class)->isEmpty())->toBeTrue();
});

test('the shop section links each product to its page', function () {
    $product = Product::factory()->create(['category' => 'Skincare']);

    $this->get(route('home'))->assertSee(route('products.show', $product), escape: false);
});

test('the existing catalogue is in the products table', function () {
    expect(Product::where('slug', 'anua-niacinamide-serum')->exists())->toBeTrue();
});

test('a product with extra photos shows a gallery with thumbnails', function () {
    $product = Product::factory()->create([
        'name' => 'Glow Serum',
        'category' => 'Test Category',
        'image_url' => 'https://res.cloudinary.com/demo/image/upload/front.jpg',
    ]);
    ProductImage::factory()->for($product)->create(['url' => 'https://res.cloudinary.com/demo/image/upload/back.jpg', 'sort_order' => 2]);
    ProductImage::factory()->for($product)->create(['url' => 'https://res.cloudinary.com/demo/image/upload/side.jpg', 'sort_order' => 1]);

    expect(array_column($product->gallery(), 'src'))->toBe([
        'https://res.cloudinary.com/demo/image/upload/front.jpg',
        'https://res.cloudinary.com/demo/image/upload/side.jpg',
        'https://res.cloudinary.com/demo/image/upload/back.jpg',
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('https://res.cloudinary.com/demo/image/upload/back.jpg')
        ->assertSee('Show photo 3');
});

test('a product with only its main image shows no thumbnails', function () {
    $product = Product::factory()->create(['category' => 'Test Category', 'image_url' => 'https://res.cloudinary.com/demo/image/upload/front.jpg']);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('https://res.cloudinary.com/demo/image/upload/front.jpg')
        ->assertDontSee('Show photo');
});

test('gallery photos show even when the product has no main image', function () {
    $product = Product::factory()->create(['category' => 'Test Category']);
    ProductImage::factory()->for($product)->create(['url' => 'https://res.cloudinary.com/demo/image/upload/only.jpg']);

    $this->get(route('products.show', $product))
        ->assertSee('https://res.cloudinary.com/demo/image/upload/only.jpg');
});
