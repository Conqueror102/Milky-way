<?php

use App\Livewire\Admin\Colors;
use App\Models\SiteContentEntry;
use App\Models\User;
use App\Support\Palette;
use App\Support\SiteContent;
use Livewire\Livewire;

function palette(): Palette
{
    app(SiteContent::class)->flush();

    return app(Palette::class);
}

test('customers cannot reach the colours page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('admin.colors'))->assertForbidden();
});

test('admins see the colours page with the ready-made palettes', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('admin.colors'))
        ->assertOk()
        ->assertSee('Main colour')
        ->assertSee('Highlight colour')
        ->assertSee('Background colour')
        ->assertSee('Forest');
});

test('the original colours in config match the stylesheet', function () {
    $css = file_get_contents(resource_path('css/app.css'));

    foreach (config('palette.roles') as $info) {
        foreach ($info['scale'] as $step => $hex) {
            preg_match("/--color-{$info['family']}-{$step}:\s*(#[0-9a-fA-F]{6});/", $css, $match);

            expect(strtolower($match[1] ?? ''))->toBe($hex, "--color-{$info['family']}-{$step}");
        }

        foreach ($info['extras'] as $name => $hex) {
            preg_match("/--color-{$name}:\s*(#[0-9a-fA-F]{6});/", $css, $match);

            expect(strtolower($match[1] ?? ''))->toBe($hex, "--color-{$name}");
        }
    }
});

test('working the shades out from the original colour gives the original shades', function () {
    foreach (config('palette.roles') as $info) {
        $anchor = $info['scale'][$info['anchor']];

        foreach ([...$info['scale'], ...$info['extras']] as $hex) {
            expect(Palette::derive($anchor, $anchor, $hex))->toBe($hex);
        }
    }
});

test('a new colour gets a full range of shades, light to dark', function () {
    $shades = palette()->shades('brand', '#1f3d2b');

    expect($shades['--color-cosmic-900'])->toBe('#1f3d2b')
        ->and($shades)->toHaveKey('--color-deep');

    $luminance = collect(range(0, 10))
        ->map(fn (int $i) => Palette::luminance($shades['--color-cosmic-'.[50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950][$i]]));

    expect($luminance->all())->toBe($luminance->sortDesc()->values()->all());
});

test('the site carries no colour overrides while the originals are in use', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('id="site-palette"', false)
        ->assertSee('<meta name="theme-color" content="#143452">', false);
});

test('admins can pick a ready-made palette and save it', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Colors::class)
        ->call('usePreset', 'forest')
        ->assertSet('colors.brand', '#1f3d2b')
        ->assertSet('colors.accent', '#b7791f')
        ->assertSee('You have unsaved changes')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('These are the colours on the site now');

    expect(SiteContentEntry::find('palette.brand')->value)->toBe('#1f3d2b')
        ->and(SiteContentEntry::find('palette.accent')->value)->toBe('#b7791f');

    palette();

    $this->get(route('home'))
        ->assertSee('id="site-palette"', false)
        ->assertSee('--color-cosmic-900:#1f3d2b', false)
        ->assertSee('--color-gold-500:#b7791f', false)
        ->assertSee('<meta name="theme-color" content="#1f3d2b">', false);
});

test('the preview follows the picks before anything is saved', function () {
    $this->actingAs(User::factory()->admin()->create());

    $component = Livewire::test(Colors::class);

    expect($component->get('previewCss'))->toContain('--color-cosmic-900:#143452');

    $component->set('colors.brand', '#4A1D3F');

    expect($component->get('colors.brand'))->toBe('#4a1d3f')
        ->and($component->get('previewCss'))->toContain('--color-cosmic-900:#4a1d3f')
        ->and(SiteContentEntry::count())->toBe(0);
});

test('half-typed colour codes are left alone and cannot be saved', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Colors::class)
        ->set('colors.brand', '#1f3')
        ->assertSet('colors.brand', '#1f3')
        ->set('colors.accent', 'orange')
        ->call('save')
        ->assertHasErrors(['colors.accent']);

    expect(SiteContentEntry::count())->toBe(0);
});

test('saving a colour as its original removes it', function () {
    SiteContentEntry::create(['key' => 'palette.brand', 'value' => '#1f3d2b']);
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Colors::class)
        ->assertSet('colors.brand', '#1f3d2b')
        ->call('useOriginal', 'brand')
        ->assertSet('colors.brand', '#143452')
        ->call('save');

    expect(SiteContentEntry::find('palette.brand'))->toBeNull()
        ->and(palette()->isCustom())->toBeFalse();
});

test('one click puts the original colours back on the site', function () {
    SiteContentEntry::create(['key' => 'palette.brand', 'value' => '#1f3d2b']);
    SiteContentEntry::create(['key' => 'palette.canvas', 'value' => '#ffffff']);
    SiteContentEntry::create(['key' => 'hero.heading', 'value' => 'Kept']);
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Colors::class)
        ->assertSee('Restore the original colours')
        ->call('restoreOriginal')
        ->assertSet('colors', palette()->originals())
        ->assertDontSee('Restore the original colours');

    expect(SiteContentEntry::where('key', 'like', 'palette.%')->count())->toBe(0)
        ->and(SiteContentEntry::find('hero.heading'))->not->toBeNull();
});

test('discard brings back the saved colours', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Colors::class)
        ->call('usePreset', 'berry')
        ->call('discard')
        ->assertSet('colors', palette()->originals());
});

test('every ready-made palette is easy to read', function () {
    foreach (palette()->presets() as $key => $preset) {
        foreach (palette()->checks($preset) as $check) {
            expect($check['ok'])->toBeTrue("{$key}: {$check['label']} is {$check['ratio']}:1");
        }
    }
});

test('the readability checks flag colours that are hard to read', function () {
    $checks = palette()->checks(['brand' => '#dddddd', 'accent' => '#e56717', 'canvas' => '#ffffff']);

    expect($checks[0]['ok'])->toBeFalse()
        ->and($checks[1]['ok'])->toBeFalse();
});
