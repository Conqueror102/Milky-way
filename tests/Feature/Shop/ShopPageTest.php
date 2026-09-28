<?php

use App\Livewire\Shop\ShopPage;
use App\Models\Category;
use App\Models\Product;
use Livewire\Livewire;

beforeEach(function () {
    // Start from an empty catalogue rather than the products the migrations add.
    Product::query()->delete();
    Category::query()->delete();
});

test('the shop page lists every active product', function () {
    Product::factory()->count(12)->sequence(fn ($sequence) => ['name' => 'Product '.($sequence->index + 1)])->create(['category' => 'Skincare']);
    Product::factory()->create(['name' => 'Hidden Cream', 'is_active' => false]);

    $this->get(route('shop'))
        ->assertOk()
        ->assertSee('Product 1')
        ->assertSee('Product 12')
        ->assertSee('12 products')
        ->assertDontSee('Hidden Cream');
});

test('the shop page filters by category from the address', function () {
    Category::factory()->create(['name' => 'Skincare']);
    Category::factory()->create(['name' => 'Spa']);
    Product::factory()->create(['name' => 'Glow Serum', 'category' => 'Skincare']);
    Product::factory()->create(['name' => 'Hot Stones', 'category' => 'Spa']);

    $this->get(route('shop', ['category' => 'Spa']))
        ->assertSee('Hot Stones')
        ->assertDontSee('Glow Serum');

    Livewire::test(ShopPage::class)
        ->call('show', 'Skincare')
        ->assertSet('category', 'Skincare')
        ->assertSee('Glow Serum')
        ->assertDontSee('Hot Stones')
        ->call('show', '')
        ->assertSee('Hot Stones');
});

test('an unknown category shows everything', function () {
    Product::factory()->create(['name' => 'Glow Serum', 'category' => 'Skincare']);

    Livewire::withQueryParams(['category' => 'Nope'])
        ->test(ShopPage::class)
        ->assertSet('category', '')
        ->assertSee('Glow Serum');
});

test('the home page shows a few products and links to the shop for the rest', function () {
    Product::factory()->count(10)->sequence(fn ($sequence) => ['name' => 'Home Product '.($sequence->index + 1), 'sort_order' => $sequence->index])->create(['category' => 'Skincare']);

    $this->get(route('home'))
        ->assertSee('Home Product 8')
        ->assertDontSee('Home Product 9')
        ->assertSee('See all products')
        ->assertSee(route('shop'));
});

test('the header shop link opens the shop page', function () {
    $this->get(route('home'))->assertSee('href="'.route('shop').'"', false);
});
