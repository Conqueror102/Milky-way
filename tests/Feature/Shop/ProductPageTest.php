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

test('preview deployments show stand-in prices so the cart can be tried', function () {
    config(['milkyway.shop.demo_prices' => true]);
    $product = Product::factory()->priceOnRequest()->create(['category' => 'Test Category']);

    expect($product->sellingPrice())->toBeGreaterThan(0)
        ->and($product->price)->toBeNull()
        ->and($product->isPurchasable())->toBeTrue();

    $this->get(route('products.show', $product))
        ->assertSee('Preview price')
        ->assertSee('Add to cart');

    Livewire::test(ProductPage::class, ['product' => $product])->call('addToCart');
    expect(app(Cart::class)->subtotal())->toBe($product->sellingPrice());
});

test('stand-in prices never show outside previews', function () {
    config(['milkyway.shop.demo_prices' => false]);
    $product = Product::factory()->priceOnRequest()->create();

    expect($product->sellingPrice())->toBeNull()
        ->and($product->isPurchasable())->toBeFalse();
});

test('vercel branch previews show stand-in prices without extra settings', function () {
    config(['milkyway.shop.demo_prices' => null]);
    $product = Product::factory()->priceOnRequest()->create(['category' => 'Test Category']);

    $this->get('https://milky-way-git-dev-team.vercel.app/products/'.$product->slug)
        ->assertSee('Preview price')
        ->assertSee('Add to cart');

    $this->get('https://milkywaycosmetics.com.ng/products/'.$product->slug)
        ->assertDontSee('Preview price')
        ->assertDontSee('Add to cart');
});

test('a real price always wins over a stand-in', function () {
    config(['milkyway.shop.demo_prices' => true]);
    $product = Product::factory()->create(['price' => 4200]);

    expect($product->sellingPrice())->toBe(4200)
        ->and($product->usesDemoPrice())->toBeFalse();
});

test('a sold out product cannot be added', function () {
    $product = Product::factory()->create(['stock' => 0]);

    $this->get(route('products.show', $product))
        ->assertSee('Sold out')
        ->assertDontSee('Add to cart');
});

test('shoppers cannot add more than is in stock', function () {
    $product = Product::factory()->create(['stock' => 2]);

    Livewire::test(ProductPage::class, ['product' => $product])
        ->call('increment')
        ->call('increment')
        ->assertSet('quantity', 2)
        ->call('addToCart')
        ->assertHasNoErrors()
        ->call('addToCart')
        ->assertHasErrors('quantity');

    expect(app(Cart::class)->count())->toBe(2);
});

test('the product page has no whatsapp hand-off for products that can be bought', function () {
    $product = Product::factory()->create(['category' => 'Test Category']);

    $this->get(route('products.show', $product))->assertDontSee('Ask about this product');
});
