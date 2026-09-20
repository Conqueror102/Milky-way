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
        'default_message' => 'Hello Milky Way Cosmetics Stores, I would like to make an enquiry about your products.',
    ],

    'socials' => [
        'instagram' => ['handle' => '@milky_cosmetics_sales', 'url' => 'https://instagram.com/milky_cosmetics_sales'],
        'tiktok' => ['handle' => '@milkywaycosmeticssales', 'url' => 'https://tiktok.com/@milkywaycosmeticssales'],
        'facebook' => ['handle' => 'Milkyway Cosmetics', 'url' => 'https://facebook.com/Milkyway-Cosmetics'],
    ],

];
