<?php

use App\Livewire\Admin\Products\Form;
use App\Livewire\Admin\Products\Index;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    config(['services.cloudinary.url' => 'cloudinary://key123:secret456@demo-cloud']);

    Http::preventStrayRequests();

    Http::fake([
        'api.cloudinary.com/*/image/upload' => fn () => $this->uploadResponse ?? Http::response([
            'secure_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/new.jpg',
            'public_id' => 'milky-way/products/new',
        ]),
        'api.cloudinary.com/*/image/destroy' => Http::response(['result' => 'ok']),
    ]);

    $this->actingAs(User::factory()->admin()->create());
});

test('admins can create a product with a photo', function () {
    Livewire::test(Form::class)
        ->set('name', 'Shea Body Butter')
        ->assertSet('slug', 'shea-body-butter')
        ->set('description', 'Rich and creamy.')
        ->set('price', '4500')
        ->set('stock', '12')
        ->set('photo', UploadedFile::fake()->image('butter.jpg'))
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.products.index'));

    $product = Product::sole();

    expect($product->name)->toBe('Shea Body Butter')
        ->and($product->slug)->toBe('shea-body-butter')
        ->and($product->price)->toBe(4500)
        ->and($product->stock)->toBe(12)
        ->and($product->image_url)->toBe('https://res.cloudinary.com/demo-cloud/image/upload/new.jpg')
        ->and($product->image_public_id)->toBe('milky-way/products/new');
});

test('products are validated', function () {
    Product::factory()->create(['slug' => 'taken']);

    Livewire::test(Form::class)
        ->set('name', '')
        ->set('slug', 'taken')
        ->set('price', '-1')
        ->set('photo', UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'))
        ->call('save')
        ->assertHasErrors(['name', 'slug', 'price', 'photo']);

    Http::assertNothingSent();
});

test('a failed upload keeps the form open with an error', function () {
    $this->uploadResponse = Http::response(['error' => ['message' => 'Invalid Signature']], 401);

    Livewire::test(Form::class)
        ->set('name', 'Shea Body Butter')
        ->set('price', '4500')
        ->set('photo', UploadedFile::fake()->image('butter.jpg'))
        ->call('save')
        ->assertHasErrors('photo')
        ->assertNoRedirect();

    expect(Product::count())->toBe(0);
});

test('admins can edit a product and replace its photo', function () {
    $product = Product::factory()->create([
        'image_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/old.jpg',
        'image_public_id' => 'milky-way/products/old',
    ]);

    Livewire::test(Form::class, ['product' => $product])
        ->assertSet('name', $product->name)
        ->set('name', 'Renamed')
        ->set('price', '999')
        ->set('photo', UploadedFile::fake()->image('new.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    $product->refresh();

    expect($product->name)->toBe('Renamed')
        ->and($product->price)->toBe(999)
        ->and($product->image_public_id)->toBe('milky-way/products/new');

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/destroy')
        && $request['public_id'] === 'milky-way/products/old');
});

test('editing without a new photo keeps the old one', function () {
    $product = Product::factory()->create(['image_public_id' => 'milky-way/products/old']);

    Livewire::test(Form::class, ['product' => $product])
        ->set('stock', '3')
        ->call('save')
        ->assertHasNoErrors();

    expect($product->refresh()->image_public_id)->toBe('milky-way/products/old');

    Http::assertNothingSent();
});

test('admins can delete a product and its photo', function () {
    $product = Product::factory()->create(['image_public_id' => 'milky-way/products/old']);

    Livewire::test(Index::class)
        ->assertSee($product->name)
        ->call('delete', $product->id);

    expect(Product::find($product->id))->toBeNull();

    Http::assertSent(fn (Request $request) => $request['public_id'] === 'milky-way/products/old');
});

test('the product list can be searched', function () {
    Product::factory()->create(['name' => 'Shea Butter']);
    Product::factory()->create(['name' => 'Rose Toner']);

    Livewire::test(Index::class)
        ->set('search', 'shea')
        ->assertSee('Shea Butter')
        ->assertDontSee('Rose Toner');
});

test('a product can be left as price on request', function () {
    $product = Product::factory()->create(['price' => 1000]);

    Livewire::test(Form::class, ['product' => $product])
        ->set('price', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($product->refresh()->price)->toBeNull();

    Livewire::test(Index::class)->assertSee('On request');
});
