<?php

use App\Livewire\Admin\Categories\Form;
use App\Livewire\Admin\Categories\Index;
use App\Livewire\Admin\Products\Form as ProductForm;
use App\Models\Category;
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
        'api.cloudinary.com/*/image/upload' => Http::response([
            'secure_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/cat.jpg',
            'public_id' => 'milky-way/products/cat',
        ]),
        'api.cloudinary.com/*/image/destroy' => Http::response(['result' => 'ok']),
    ]);

    $this->actingAs(User::factory()->admin()->create());
});

test('the launch categories are seeded with their descriptions and photos', function () {
    $skincare = Category::where('name', 'Skincare')->sole();

    expect(Category::count())->toBeGreaterThanOrEqual(7)
        ->and($skincare->description)->toContain('Cleansers')
        ->and($skincare->imageSrc())->toBe('/images/categories/skincare.jpg');
});

test('only admins can reach the categories pages', function () {
    $this->get(route('admin.categories.index'))->assertOk()->assertSee('Skincare');

    $this->actingAs(User::factory()->create());
    $this->get(route('admin.categories.index'))->assertForbidden();
});

test('admins can create a category with a photo', function () {
    Livewire::test(Form::class)
        ->set('name', 'Hair Care')
        ->set('description', 'Oils, creams and treatments for hair.')
        ->set('photo', UploadedFile::fake()->image('hair.jpg'))
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.categories.index'));

    $category = Category::where('name', 'Hair Care')->sole();

    expect($category->image_url)->toBe('https://res.cloudinary.com/demo-cloud/image/upload/cat.jpg')
        ->and($category->sort_order)->toBe((int) Category::max('sort_order'));
});

test('category names must be unique and free of quotes', function (string $name) {
    Livewire::test(Form::class)
        ->set('name', $name)
        ->call('save')
        ->assertHasErrors('name');
})->with(['Skincare', "Men's Care", '']);

test('renaming a category renames it on its products', function () {
    $category = Category::where('name', 'Skincare')->sole();
    $product = Product::factory()->create(['category' => 'Skincare']);

    Livewire::test(Form::class, ['category' => $category])
        ->set('name', 'Skin Care')
        ->call('save')
        ->assertHasNoErrors();

    expect($product->fresh()->category)->toBe('Skin Care')
        ->and(Product::where('category', 'Skincare')->exists())->toBeFalse();
});

test('the photo of a built-in category can be replaced or removed', function () {
    $category = Category::where('name', 'Skincare')->sole();

    Livewire::test(Form::class, ['category' => $category])
        ->set('removePhoto', true)
        ->call('save')
        ->assertHasNoErrors();

    expect($category->fresh()->imageSrc())->toBeNull();
});

test('deleting a category with products needs somewhere to move them', function () {
    $category = Category::where('name', 'Wholesale')->sole();
    $product = Product::factory()->create(['category' => 'Wholesale']);

    Livewire::test(Index::class)
        ->call('confirmDelete', $category->id)
        ->call('delete')
        ->assertHasErrors('moveTo')
        ->set('moveTo', 'Skincare')
        ->call('delete')
        ->assertHasNoErrors();

    expect(Category::find($category->id))->toBeNull()
        ->and($product->fresh()->category)->toBe('Skincare');
});

test('an empty category can be deleted and its photo removed from Cloudinary', function () {
    $category = Category::factory()->create(['image_public_id' => 'milky-way/products/old-cat']);

    Livewire::test(Index::class)
        ->call('confirmDelete', $category->id)
        ->call('delete')
        ->assertHasNoErrors();

    expect(Category::find($category->id))->toBeNull();

    Http::assertSent(fn (Request $request) => $request['public_id'] === 'milky-way/products/old-cat');
});

test('categories can be reordered', function () {
    $first = Category::ordered()->first();
    $second = Category::ordered()->skip(1)->first();

    Livewire::test(Index::class)->call('move', $second->id, -1);

    expect(Category::ordered()->first()->id)->toBe($second->id)
        ->and(Category::ordered()->skip(1)->first()->id)->toBe($first->id);
});

test('products must be filed under an existing category', function () {
    Livewire::test(ProductForm::class)
        ->set('name', 'Mystery Item')
        ->set('category', 'Not A Category')
        ->call('save')
        ->assertHasErrors('category');
});

test('the shop shows categories in the admin order with their descriptions', function () {
    $category = Category::factory()->create(['name' => 'Hair Care', 'description' => 'Oils for every curl.', 'image_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/hair.jpg']);
    Category::where('name', 'Skincare')->update(['description' => 'Fresh skincare words.']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Hair Care')
        ->assertSee('Oils for every curl.')
        ->assertSee('https://res.cloudinary.com/demo-cloud/image/upload/hair.jpg')
        ->assertSee('Fresh skincare words.');
});
