<?php

test('the homepage renders the hero', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Your Beauty.', escape: false)
        ->assertSee('Passion', escape: false)
        ->assertSee('Wholesale &amp; Retail', escape: false);
});

test('the homepage links to whatsapp with a prefilled enquiry', function () {
    $expected = 'https://wa.me/'.config('milkyway.whatsapp.number')
        .'?text='.rawurlencode(config('milkyway.whatsapp.default_message'));

    $this->get(route('home'))->assertSee($expected, escape: false);
});

test('the category section lists every category', function () {
    $response = $this->get(route('home'));

    foreach (['Skincare', 'Beauty &amp; Cosmetics', 'Health &amp; Beauty', 'Body Enhancement', 'Spa &amp; Massage', 'Wholesale'] as $category) {
        $response->assertSee($category, escape: false);
    }
});

test('each category card deep links to whatsapp with its own enquiry', function () {
    $expected = 'https://wa.me/'.config('milkyway.whatsapp.number')
        .'?text='.rawurlencode('Hello Milky Way Cosmetics Stores, I would like to see what you have available under Spa & Massage.');

    $this->get(route('home'))->assertSee($expected, escape: false);
});

test('the why section lists all six reasons', function () {
    $response = $this->get(route('home'));

    foreach ([
        'Guaranteed Quality',
        'Wholesale &amp; Retail',
        'Wide Variety',
        'Prime Location',
        '24/7 Availability',
        'Dedicated Customer Support',
    ] as $reason) {
        $response->assertSee($reason, escape: false);
    }
});

test('the audience section names everyone it serves', function () {
    $response = $this->get(route('home'));

    foreach ([
        'Individual Beauty Consumers',
        'Retail Customers',
        'Wholesale Buyers',
        'Beauty Product Resellers',
        'Salon Owners',
        'Spa &amp; Massage Businesses',
        'Makeup Artists &amp; Beauty Professionals',
        'Beauty Entrepreneurs',
    ] as $audience) {
        $response->assertSee($audience, escape: false);
    }
});

test('the wholesale section opens whatsapp with a qualifying enquiry', function () {
    $expected = 'https://wa.me/'.config('milkyway.whatsapp.number').'?text='.rawurlencode(
        'Hello Milky Way Cosmetics Stores. I run a [salon / shop / resale business] in [area] '
        .'and I would like wholesale prices for [category]. I buy roughly [quantity] at a time.'
    );

    $this->get(route('home'))
        ->assertSee($expected, escape: false)
        ->assertSee('id="wholesale"', escape: false);
});

test('the ordering steps appear in order', function () {
    $html = $this->get(route('home'))->assertOk()->getContent();

    $positions = [];
    foreach (['Browse products', 'Check availability', 'Contact us', 'Confirm your order', 'Receive your order'] as $step) {
        $positions[$step] = strpos($html, $step);
        expect($positions[$step])->not->toBeFalse("Missing step: {$step}");
    }

    expect(array_values($positions))->toBe(collect($positions)->values()->sort()->values()->all());
});

test('the delivery section names every area it serves', function () {
    $response = $this->get(route('home'));

    foreach (config('milkyway.delivery_areas') as $area) {
        $response->assertSee($area, escape: false);
    }

    $response->assertSee(
        'https://wa.me/'.config('milkyway.whatsapp.number').'?text='.rawurlencode(
            'Hello Milky Way Cosmetics Stores. I would like to ask about delivery to [your area].'
        ),
        escape: false
    );
});

test('the about section states where the shop is and what it distributes', function () {
    $response = $this->get(route('home'));

    $response->assertSee(config('milkyway.address.line'), escape: false)
        ->assertSee(config('milkyway.hours'), escape: false)
        ->assertSee('id="about"', escape: false);

    foreach (['Health', 'Beauty', 'Skincare', 'Body Enhancement', 'Spa'] as $item) {
        $response->assertSee($item, escape: false);
    }
});

test('the contact section carries the address, hours and dialable phone', function () {
    $this->get(route('home'))
        ->assertSee('id="contact"', escape: false)
        ->assertSee(config('milkyway.address.line'), escape: false)
        ->assertSee(config('milkyway.hours'), escape: false)
        ->assertSee('tel:'.config('milkyway.phone_dial'), escape: false)
        ->assertSee('https://www.google.com/maps/search/?api=1&amp;query='.rawurlencode(config('milkyway.map_query')), escape: false);
});

test('every scrolling section opts in to the reveal system', function () {
    $html = $this->get(route('home'))->assertOk()->getContent();

    foreach (['shop', 'why', 'who', 'wholesale', 'how', 'delivery', 'about', 'contact'] as $id) {
        preg_match('/<section id="'.$id.'".*?<\/section>/s', $html, $section);

        expect($section)->not->toBeEmpty("Section #{$id} is missing")
            ->and($section[0])->toContain('data-reveal');
    }
});

test('the hero enters with css alone and the layout guards the scripted reveals', function () {
    $html = $this->get(route('home'))->assertOk()->getContent();

    expect($html)
        ->toContain('data-enter')
        ->toContain("document.documentElement.classList.add('motion-ready')")
        ->toContain('prefers-reduced-motion: reduce');

    preg_match('/<section id="top".*?<\/section>/s', $html, $hero);

    expect($hero[0])->not->toContain('data-reveal');
});
