<?php

test('the homepage is indexable and carries its own title and description', function () {
    $html = $this->get(route('home'))->assertOk()->getContent();

    expect($html)
        ->toContain('<meta name="robots" content="index, follow, max-image-preview:large" />')
        ->toContain('<title>')
        ->toContain(e(config('milkyway.seo.title')))
        ->toContain('<meta name="description" content="'.e(config('milkyway.seo.description')).'" />');
});

test('the canonical url and open graph image are absolute and follow APP_URL', function () {
    config(['app.url' => 'https://milkywaycosmetics.example']);

    $this->get(route('home'))
        ->assertSee('<link rel="canonical" href="https://milkywaycosmetics.example/" />', escape: false)
        ->assertSee('<meta property="og:url" content="https://milkywaycosmetics.example/" />', escape: false)
        ->assertSee('<meta property="og:image" content="https://milkywaycosmetics.example/images/og-image.jpg" />', escape: false)
        ->assertSee('<meta name="twitter:image" content="https://milkywaycosmetics.example/images/og-image.jpg" />', escape: false)
        ->assertSee('<meta name="twitter:card" content="summary_large_image" />', escape: false);
});

test('the open graph image really is 1200 by 630 and light enough for link previews', function () {
    [$width, $height] = getimagesize(public_path('images/og-image.jpg'));

    expect([$width, $height])->toBe([1200, 630])
        ->and(filesize(public_path('images/og-image.jpg')))->toBeLessThan(300 * 1024);
});

test('the structured data is valid json and describes the real business', function () {
    $html = $this->get(route('home'))->getContent();

    preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $match);
    $graph = json_decode($match[1], true, flags: JSON_THROW_ON_ERROR);

    $business = collect($graph['@graph'])->firstWhere('@id', rtrim(config('app.url'), '/').'/#business');

    expect($graph['@context'])->toBe('https://schema.org')
        ->and($business['name'])->toBe(config('app.name'))
        ->and($business['telephone'])->toBe(config('milkyway.phone_dial'))
        ->and($business['address']['streetAddress'])->toBe(config('milkyway.address.line'))
        ->and($business['address']['addressCountry'])->toBe('NG')
        ->and($business['openingHoursSpecification'][0]['dayOfWeek'])->toHaveCount(7)
        ->and($business['sameAs'])->each->toStartWith('https://');
});

test('robots.txt points crawlers at the sitemap and keeps them out of the app', function () {
    config(['app.url' => 'https://milkywaycosmetics.example']);

    $response = $this->get(route('seo.robots'))->assertOk();

    expect($response->headers->get('Content-Type'))->toContain('text/plain')
        ->and($response->getContent())
        ->toContain('Sitemap: https://milkywaycosmetics.example/sitemap.xml')
        ->toContain('Disallow: /dashboard')
        ->toContain('Disallow: /settings');
});

test('the sitemap is well-formed xml listing the homepage', function () {
    config(['app.url' => 'https://milkywaycosmetics.example']);

    $response = $this->get(route('seo.sitemap'))->assertOk();
    $xml = simplexml_load_string($response->getContent());

    expect($response->headers->get('Content-Type'))->toContain('xml')
        ->and($xml)->not->toBeFalse()
        ->and((string) $xml->url->loc)->toBe('https://milkywaycosmetics.example/');
});

test('auth and app pages stay out of search results', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex, nofollow" />', escape: false);
});

test('every icon the head links to exists', function () {
    foreach (['favicon.ico', 'favicon-16.png', 'favicon-32.png', 'apple-touch-icon.png', 'icon-192.png', 'icon-512.png', 'icon-maskable-512.png', 'site.webmanifest'] as $file) {
        expect(public_path($file))->toBeFile();
    }

    $manifest = json_decode(file_get_contents(public_path('site.webmanifest')), true, flags: JSON_THROW_ON_ERROR);

    foreach ($manifest['icons'] as $icon) {
        expect(public_path(ltrim($icon['src'], '/')))->toBeFile();
    }
});
