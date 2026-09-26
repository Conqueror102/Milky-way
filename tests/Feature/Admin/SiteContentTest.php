<?php

use App\Livewire\Admin\Site\Edit;
use App\Livewire\Admin\Site\Index;
use App\Models\SiteContentEntry;
use App\Models\User;
use App\Support\SiteContent;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function () {
    config(['services.cloudinary.url' => 'cloudinary://key123:secret456@demo-cloud']);

    Http::preventStrayRequests();

    Http::fake([
        // Tests can override the upload reply by setting $this->uploadResponse.
        'api.cloudinary.com/*/image/upload' => fn () => $this->uploadResponse ?? Http::response([
            'secure_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/v1/milky-way/site/new.jpg',
            'public_id' => 'milky-way/site/new',
        ]),
        'api.cloudinary.com/*/image/destroy' => Http::response(['result' => 'ok']),
    ]);
});

/**
 * Pretend an admin already uploaded a photo for a slot.
 */
function uploadedPhoto(string $key, string $publicId = 'milky-way/site/old'): void
{
    SiteContentEntry::create([
        'key' => $key,
        'value' => "https://res.cloudinary.com/demo-cloud/image/upload/v1/{$publicId}.jpg",
        'public_id' => $publicId,
        'alt' => 'An old photo',
    ]);

    app(SiteContent::class)->flush();
}

test('customers cannot reach the site content admin', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('admin.site.index'))->assertForbidden();
    $this->get(route('admin.site.edit', 'hero'))->assertForbidden();
});

test('admins see every section with its screenshot', function () {
    $this->actingAs(User::factory()->admin()->create());

    $response = $this->get(route('admin.site.index'))->assertOk();

    foreach (array_keys(config('site_content.sections')) as $section) {
        $response->assertSee(route('admin.site.edit', $section))
            ->assertSee("/images/admin/sections/{$section}.jpg");
    }
});

test('every section has a screenshot for the admin', function () {
    foreach (array_keys(config('site_content.sections')) as $section) {
        expect(public_path("images/admin/sections/{$section}.jpg"))->toBeFile();
    }
});

test('every default photo exists', function () {
    foreach (array_keys(config('site_content.sections')) as $section) {
        foreach (app(SiteContent::class)->fields($section) as $field) {
            if ($field['type'] === 'image') {
                expect(public_path(ltrim($field['default'], '/')))->toBeFile();
            }
        }
    }
});

test('the index counts what an admin has changed', function () {
    $this->actingAs(User::factory()->admin()->create());
    SiteContentEntry::create(['key' => 'why.heading', 'value' => 'Changed']);

    Livewire::test(Index::class)->assertSee('1 change');
});

test('admins can open every section', function (string $section) {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('admin.site.edit', $section))->assertOk();
})->with(['hero', 'categories', 'why', 'audience', 'wholesale', 'how', 'delivery', 'about', 'contact', 'footer', 'business']);

test('an unknown section is not found', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('admin.site.edit', 'nope'))->assertNotFound();
});

test('the site shows the original copy until an admin changes it', function () {
    $this->get(route('home'))
        ->assertSee('One store, every')
        ->assertSee('Quality beauty, made');

    expect(SiteContentEntry::count())->toBe(0);
});

test('saved text shows on the home page', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Edit::class, ['section' => 'why'])
        ->set('values.heading', 'Beauty you can trust,')
        ->set('values.heading_accent', 'always')
        ->set('values.reason_1_title', 'Straight from the source')
        ->call('save')
        ->assertHasNoErrors();

    $this->get(route('home'))
        ->assertSee('Beauty you can trust,')
        ->assertSee('always')
        ->assertSee('Straight from the source')
        ->assertDontSee('Guaranteed Quality');

    expect(SiteContentEntry::count())->toBe(3);
});

test('putting back the original text removes the change', function () {
    $this->actingAs(User::factory()->admin()->create());
    SiteContentEntry::create(['key' => 'why.heading', 'value' => 'Changed']);

    Livewire::test(Edit::class, ['section' => 'why'])
        ->assertSet('values.heading', 'Changed')
        ->call('useDefault', 'heading')
        ->assertSet('values.heading', 'Quality beauty, made')
        ->call('save');

    expect(SiteContentEntry::find('why.heading'))->toBeNull();
    $this->get(route('home'))->assertSee('Quality beauty, made');
});

test('a required field left empty falls back to the original', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Edit::class, ['section' => 'why'])
        ->set('values.heading', '   ')
        ->call('save')
        ->assertSet('values.heading', 'Quality beauty, made');

    expect(SiteContentEntry::count())->toBe(0);
});

test('an optional field can be emptied', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Edit::class, ['section' => 'wholesale'])
        ->set('values.tell_note', '')
        ->call('save');

    expect(SiteContentEntry::find('wholesale.tell_note')->value)->toBe('');
    $this->get(route('home'))->assertDontSee('Four lines in your first message');
});

test('lists are saved one item per line', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Edit::class, ['section' => 'about'])
        ->set('values.what_items', "Hair care\n\n  Fragrance  \n")
        ->call('save');

    expect(SiteContentEntry::find('about.what_items')->value)->toBe("Hair care\nFragrance");
    $this->get(route('home'))->assertSee('Hair care')->assertSee('Fragrance');
});

test('bold markers in the hero text become bold and nothing else is trusted', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Edit::class, ['section' => 'hero'])
        ->set('values.intro', 'Great **prices** <script>alert(1)</script>')
        ->call('save');

    $this->get(route('home'))
        ->assertSee('<span class="font-semibold text-cosmic-900">prices</span>', escape: false)
        ->assertDontSee('<script>alert(1)</script>', escape: false);
});

test('an uploaded photo replaces the original on the site', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Edit::class, ['section' => 'hero'])
        ->set('photos.slide_2', UploadedFile::fake()->image('new.jpg'))
        ->set('alts.slide_2', 'A woman holding a jar of cream')
        ->call('save')
        ->assertHasNoErrors();

    $entry = SiteContentEntry::find('hero.slide_2');

    expect($entry->value)->toBe('https://res.cloudinary.com/demo-cloud/image/upload/v1/milky-way/site/new.jpg')
        ->and($entry->public_id)->toBe('milky-way/site/new')
        ->and($entry->alt)->toBe('A woman holding a jar of cream');

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'image/upload')
        && str_contains($request->body(), 'milky-way/site'));

    $this->get(route('home'))
        ->assertSee('https://res.cloudinary.com/demo-cloud/image/upload/f_auto,q_auto,c_limit,w_640/v1/milky-way/site/new.jpg 640w', escape: false)
        ->assertSee('A woman holding a jar of cream')
        ->assertDontSee('/images/hero/slide-2-640.jpg')
        ->assertSee('/images/hero/slide-1-640.jpg');
});

test('replacing an uploaded photo deletes the old one from Cloudinary', function () {
    $this->actingAs(User::factory()->admin()->create());
    uploadedPhoto('about.photo');

    Livewire::test(Edit::class, ['section' => 'about'])
        ->set('photos.photo', UploadedFile::fake()->image('new.jpg'))
        ->call('save');

    expect(SiteContentEntry::find('about.photo')->public_id)->toBe('milky-way/site/new');
    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'image/destroy')
        && $request['public_id'] === 'milky-way/site/old');
});

test('an uploaded photo can be swapped back to the original', function () {
    $this->actingAs(User::factory()->admin()->create());
    uploadedPhoto('wholesale.photo');

    Livewire::test(Edit::class, ['section' => 'wholesale'])
        ->call('restorePhoto', 'photo')
        ->assertSee('Original, on save')
        ->call('save');

    expect(SiteContentEntry::find('wholesale.photo'))->toBeNull();
    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'image/destroy'));
    $this->get(route('home'))->assertSee('/images/wholesale-strip-1200.jpg');
});

test('a failed upload changes nothing', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->uploadResponse = Http::response(['error' => ['message' => 'Nope']], 400);

    Livewire::test(Edit::class, ['section' => 'why'])
        ->set('values.heading', 'Changed')
        ->set('photos.photo_1', UploadedFile::fake()->image('new.jpg'))
        ->call('save')
        ->assertHasErrors('photos.photo_1');

    expect(SiteContentEntry::count())->toBe(0);
});

test('photos must be images', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Edit::class, ['section' => 'why'])
        ->set('photos.photo_1', UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'))
        ->call('save')
        ->assertHasErrors('photos.photo_1');
});

test('the form knows when it has unsaved changes', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Edit::class, ['section' => 'why'])
        ->assertSee('Everything here is live on the site.')
        ->set('values.heading', 'Changed')
        ->assertSee('You have unsaved changes.')
        ->call('save')
        ->assertSee('Everything here is live on the site.');
});

test('business details change across the site', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Edit::class, ['section' => 'business'])
        ->set('values.phone', '+234 800 000 0000')
        ->set('values.whatsapp', '234 800 000 0001')
        ->set('values.hours', 'Open 9am to 9pm daily')
        ->set('values.delivery_areas', "Lagos\nIbadan")
        ->call('save');

    $this->get(route('home'))
        ->assertSee('+234 800 000 0000')
        ->assertSee('tel:+2348000000000', escape: false)
        ->assertSee('https://wa.me/2348000000001', escape: false)
        ->assertSee('Open 9am to 9pm daily')
        ->assertSee('Ibadan')
        ->assertDontSee('+234 816 182 3482');
});

test('the site still renders before the content table exists', function () {
    Schema::drop('site_content_entries');
    app(SiteContent::class)->flush();

    $this->get(route('home'))->assertOk()->assertSee('One store, every');
});
