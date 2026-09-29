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

    'whatsapp' => [
        'number' => '2348161823482',
        'default_message' => 'Hello Milkyway Cosmetics Stores, I would like to make an enquiry about your products.',
    ],

    /*
    | The social accounts an admin can link in the footer at /admin/socials, with the
    | link and whether it shows until an admin saves their own. WhatsApp without a
    | link uses the business WhatsApp number.
    */
    'socials' => [
        'instagram' => ['label' => 'Instagram', 'url' => 'https://instagram.com/milky_cosmetics_sales', 'show' => true],
        'tiktok' => ['label' => 'TikTok', 'url' => 'https://tiktok.com/@milkywaycosmeticssales', 'show' => true],
        'facebook' => ['label' => 'Facebook', 'url' => 'https://facebook.com/Milkyway-Cosmetics', 'show' => true],
        'whatsapp' => ['label' => 'WhatsApp', 'url' => '', 'show' => true],
        'x' => ['label' => 'X (Twitter)', 'url' => '', 'show' => false],
        'youtube' => ['label' => 'YouTube', 'url' => '', 'show' => false],
        'snapchat' => ['label' => 'Snapchat', 'url' => '', 'show' => false],
        'linkedin' => ['label' => 'LinkedIn', 'url' => '', 'show' => false],
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
        'description' => 'Quality skincare, beauty, body enhancers and spa products at wholesale and retail prices. Shop with us or stock your salon, spa or beauty business from Amuwo-Odofin, Lagos.',
        'image' => '/images/og-image.jpg',
        'image_alt' => 'Milkyway Cosmetics Stores: Your Beauty. Our Passion.',
        'locale' => 'en_NG',
    ],

    /*
    |--------------------------------------------------------------------------
    | Shop
    |--------------------------------------------------------------------------
    |
    | demo_prices gives every product without a price a stand-in price, so the
    | cart and checkout can be tried end to end. SHOP_DEMO_PRICES decides when
    | set. Otherwise it is on for Vercel preview deployments, which are known by
    | VERCEL_ENV or, when the container doesn't receive that, by their branch
    | address (*-git-*.vercel.app), and off everywhere else. It never writes
    | prices to the database.
    |
    | payment_gateway names the online payment provider. Paystack switches on by
    | itself once PAYSTACK_SECRET_KEY is set; until then the payment step shows
    | the order total with payment marked as coming soon.
    |
    */

    'shop' => [
        'demo_prices' => env('SHOP_DEMO_PRICES', env('VERCEL_ENV') ? env('VERCEL_ENV') === 'preview' : null),

        'payment_gateway' => env('SHOP_PAYMENT_GATEWAY', env('PAYSTACK_SECRET_KEY') ? 'paystack' : null),
    ],

];
