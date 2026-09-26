<?php

use App\Livewire\Admin\Products\Form;
use App\Livewire\Admin\Products\Index;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    config(['services.cloudinary.url' => 'cloudinary://key123:secret456@demo-cloud']);

    Http::preventStrayRequests();

    Http::fake([
        // Tests can override the upload reply by setting $this->uploadResponse.
        'api.cloudinary.com/*/image/upload' => function (Request $request) {
            $response = $this->uploadResponse ?? null;

            if ($response instanceof ResponseSequence) {
                return $response($request);
            }

            return $response ?? Http::response([
                'secure_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/new.jpg',
                'public_id' => 'milky-way/products/new',
            ]);
        },
        'api.cloudinary.com/*/image/destroy' => Http::response(['result' => 'ok']),
    ]);

    $this->actingAs(User::factory()->admin()->create());
});

test('admins can create a product with a photo', function () {
    Livewire::test(Form::class)
        ->set('name', 'Shea Body Butter')
        ->assertSet('slug', 'shea-body-butter')
        ->set('category', 'Skincare')
        ->set('description', 'Rich and creamy.')
        ->set('price', '4500')
        ->set('stock', '12')
        ->set('photo', UploadedFile::fake()->image('butter.jpg'))
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.products.index'));

    $product = Product::where('slug', 'shea-body-butter')->sole();

    expect($product->name)->toBe('Shea Body Butter')
        ->and($product->slug)->toBe('shea-body-butter')
        ->and($product->category)->toBe('Skincare')
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
        ->assertHasErrors(['name', 'slug', 'category', 'price', 'photo']);

    Http::assertNothingSent();
});

test('a failed upload keeps the form open with an error', function () {
    $this->uploadResponse = Http::response(['error' => ['message' => 'Invalid Signature']], 401);

    Livewire::test(Form::class)
        ->set('name', 'Shea Body Butter')
        ->set('category', 'Skincare')
        ->set('price', '4500')
        ->set('photo', UploadedFile::fake()->image('butter.jpg'))
        ->call('save')
        ->assertHasErrors('photo')
        ->assertNoRedirect();

    expect(Product::where('name', 'Shea Body Butter')->exists())->toBeFalse();
});

test('admins can edit a product and replace its photo', function () {
    $product = Product::factory()->create([
        'image_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/old.jpg',
        'image_public_id' => 'milky-way/products/old',
    ]);

    Livewire::test(Form::class, ['product' => $product->fresh()])
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

    Livewire::test(Form::class, ['product' => $product->fresh()])
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

    Livewire::test(Form::class, ['product' => $product->fresh()])
        ->set('price', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($product->refresh()->price)->toBeNull();

    Livewire::test(Index::class)->assertSee('On request');
});

test('admins can add several gallery photos at once', function () {
    $product = Product::factory()->create();

    Livewire::test(Form::class, ['product' => $product->fresh()])
        ->set('photos', [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')])
        ->call('save')
        ->assertHasNoErrors();

    $images = $product->images()->get();

    expect($images)->toHaveCount(2)
        ->and($images->pluck('public_id')->all())->toBe(['milky-way/products/new', 'milky-way/products/new'])
        ->and($images[0]->sort_order)->toBeLessThan($images[1]->sort_order)
        ->and($product->fresh()->image_public_id)->toBeNull();
});

test('gallery photos can be reordered', function () {
    $product = Product::factory()->create();
    $first = ProductImage::factory()->for($product)->create(['sort_order' => 0]);
    $second = ProductImage::factory()->for($product)->create(['sort_order' => 1]);
    $third = ProductImage::factory()->for($product)->create(['sort_order' => 2]);

    Livewire::test(Form::class, ['product' => $product->fresh()])
        ->call('moveImage', $third->id, -1);

    expect($product->images()->pluck('id')->all())->toBe([$first->id, $third->id, $second->id]);

    Livewire::test(Form::class, ['product' => $product->fresh()])
        ->call('moveImage', $first->id, -1);

    expect($product->images()->pluck('id')->all())->toBe([$first->id, $third->id, $second->id]);
});

test('gallery photos can be deleted', function () {
    $product = Product::factory()->create();
    $image = ProductImage::factory()->for($product)->create(['public_id' => 'milky-way/products/extra']);

    Livewire::test(Form::class, ['product' => $product->fresh()])
        ->call('deleteImage', $image->id);

    expect(ProductImage::find($image->id))->toBeNull();

    Http::assertSent(fn (Request $request) => $request['public_id'] === 'milky-way/products/extra');
});

test('another product\'s photo cannot be deleted from this form', function () {
    $product = Product::factory()->create();
    $other = ProductImage::factory()->create();

    expect(fn () => Livewire::test(Form::class, ['product' => $product->fresh()])->call('deleteImage', $other->id))
        ->toThrow(ModelNotFoundException::class);

    expect(ProductImage::find($other->id))->not->toBeNull();
});

test('a failed gallery upload saves nothing and cleans up', function () {
    $product = Product::factory()->create(['name' => 'Original']);
    $this->uploadResponse = Http::sequence()
        ->push(['secure_url' => 'https://x/first.jpg', 'public_id' => 'milky-way/products/first'])
        ->push(['error' => ['message' => 'Boom']], 500);

    Livewire::test(Form::class, ['product' => $product->fresh()])
        ->set('name', 'Changed')
        ->set('photos', [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')])
        ->call('save')
        ->assertHasErrors('photos');

    expect($product->fresh()->name)->toBe('Original')
        ->and($product->images()->count())->toBe(0);

    Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/destroy')
        && $request['public_id'] === 'milky-way/products/first');
});

test('deleting a product removes its gallery photos from Cloudinary', function () {
    $product = Product::factory()->create(['image_public_id' => 'milky-way/products/main']);
    ProductImage::factory()->for($product)->create(['public_id' => 'milky-way/products/extra']);

    Livewire::test(Index::class)->call('delete', $product->id);

    Http::assertSent(fn (Request $request) => $request['public_id'] === 'milky-way/products/main');
    Http::assertSent(fn (Request $request) => $request['public_id'] === 'milky-way/products/extra');
});

test('stock can be left empty to stop tracking it', function () {
    $product = Product::factory()->create(['stock' => 4]);

    Livewire::test(Form::class, ['product' => $product->fresh()])
        ->set('stock', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($product->fresh()->stock)->toBeNull();

    Livewire::test(Index::class)->assertSee('Not tracked');
});

test('sold out products are flagged in the list', function () {
    Product::factory()->create(['stock' => 0, 'name' => 'Empty Shelf']);

    Livewire::test(Index::class)->assertSee('Sold out');
});
