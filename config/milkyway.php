<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Business Details
    |--------------------------------------------------------------------------
    |
    | Single source of truth for the contact and location information that is
    | repeated across the marketing pages. Update it here, not in the views.
    |
    */

    'tagline' => 'Your Beauty. Our Passion.',

    'phone' => '+234 816 182 3482',

    'phone_dial' => '+2348161823482',

    'address' => [
        'line' => 'C003 Bornu Plaza, Tradefair Complex',
        'area' => 'Amuwo-Odofin, Lagos, Nigeria',
        'locality' => 'Amuwo-Odofin',
        'region' => 'Lagos',
        'country' => 'NG',
    ],

    'hours' => 'Open 24 Hours, Monday to Sunday',

    /*
    | Swap this for a Google Business Profile link if you have one — a plain address
    | search drops people at Tradefair Complex rather than at the unit.
    */
    'map_query' => 'C003 Bornu Plaza, Tradefair Complex, Amuwo-Odofin, Lagos, Nigeria',

    'delivery_areas' => ['Lagos', 'Ogun', 'Abuja', 'Accra, Ghana'],

    'whatsapp' => [
        'number' => '2348161823482',
        'default_message' => 'Hello Milkyway Cosmetics Stores, I would like to make an enquiry about your products.',
    ],

    'socials' => [
        'instagram' => ['handle' => '@milky_cosmetics_sales', 'url' => 'https://instagram.com/milky_cosmetics_sales'],
        'tiktok' => ['handle' => '@milkywaycosmeticssales', 'url' => 'https://tiktok.com/@milkywaycosmeticssales'],
        'facebook' => ['handle' => 'Milkyway Cosmetics', 'url' => 'https://facebook.com/Milkyway-Cosmetics'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Search & Sharing
    |--------------------------------------------------------------------------
    |
    | Defaults for the homepage. `image` is a path under /public and is made
    | absolute from APP_URL, so APP_URL must be the live domain in production.
    |
    */

    'seo' => [
        'title' => 'Milkyway Cosmetics Stores | Wholesale & Retail Beauty in Lagos',
        'description' => 'Quality skincare, beauty, body enhancement and spa products at wholesale and retail prices. Shop with us or stock your salon, spa or beauty business from Amuwo-Odofin, Lagos.',
        'image' => '/images/og-image.jpg',
        'image_alt' => 'Milkyway Cosmetics Stores: Your Beauty. Our Passion.',
        'locale' => 'en_NG',
        'theme_color' => '#143452',
    ],

    /*
    |--------------------------------------------------------------------------
    | Shop
    |--------------------------------------------------------------------------
    |
    | demo_prices gives every product without a price a stand-in price, so the
    | cart and checkout can be tried end to end. It is on for Vercel preview
    | deployments and off everywhere else unless SHOP_DEMO_PRICES says so; it
    | never writes prices to the database.
    |
    | payment_gateway names the online payment provider. Paystack switches on by
    | itself once PAYSTACK_SECRET_KEY is set; until then the payment step shows
    | the order total with payment marked as coming soon.
    |
    */

    'shop' => [
        'demo_prices' => (bool) env('SHOP_DEMO_PRICES', env('VERCEL_ENV') === 'preview'),

        'payment_gateway' => env('SHOP_PAYMENT_GATEWAY', env('PAYSTACK_SECRET_KEY') ? 'paystack' : null),
    ],

];
