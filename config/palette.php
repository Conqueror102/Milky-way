<?php

/*
|--------------------------------------------------------------------------
| Site Colours
|--------------------------------------------------------------------------
|
| The three colours an admin can change from /admin/colors. Each one drives a
| whole family of shades in resources/css/app.css: the admin picks the colour
| at `anchor`, and App\Support\Palette works out every other shade so it sits
| the same way against the new colour as the original shade did against the
| original colour. `scale` must match the @theme block in app.css, which is
| what the site shows until an admin saves something else. `extras` are CSS
| variables outside the numbered scale that follow the same colour.
|
| `presets` are the ready-made palettes offered at the top of the page.
|
*/

return [

    'roles' => [

        'brand' => [
            'label' => 'Main colour',
            'help' => 'Headings, text, buttons, the menu and the dark sections.',
            'family' => 'cosmic',
            'anchor' => 900,
            'scale' => [
                50 => '#f3f8fd',
                100 => '#e6eff9',
                200 => '#cee0f3',
                300 => '#b0cdec',
                400 => '#8cb6e0',
                500 => '#6f9fd0',
                600 => '#5686b6',
                700 => '#416d99',
                800 => '#31567b',
                900 => '#143452',
                950 => '#122a42',
            ],
            'extras' => [
                // The "Who we serve" band
                'deep' => '#052a58',
            ],
        ],

        'accent' => [
            'label' => 'Highlight colour',
            'help' => 'Small labels, icons, the glow in the footer and other touches that catch the eye.',
            'family' => 'gold',
            'anchor' => 500,
            'scale' => [
                50 => '#fdf6f0',
                100 => '#fbe9db',
                200 => '#f6d2b6',
                300 => '#f0b58a',
                400 => '#eb8b4c',
                500 => '#e56717',
                600 => '#ca4f0f',
                700 => '#a73d10',
                800 => '#873214',
                900 => '#6f2c14',
                950 => '#3c1307',
            ],
            'extras' => [],
        ],

        'canvas' => [
            'label' => 'Background colour',
            'help' => 'The page behind everything. A very light colour reads best.',
            'family' => 'cream',
            'anchor' => 50,
            'scale' => [
                50 => '#fcfbf7',
                100 => '#f0eee6',
                200 => '#e1ded0',
                300 => '#d0cab2',
                400 => '#bab291',
                500 => '#a59b75',
                600 => '#8c825c',
                700 => '#736a48',
                800 => '#5b5336',
                900 => '#494229',
                950 => '#2e2916',
            ],
            'extras' => [],
        ],

    ],

    'presets' => [
        'original' => ['label' => 'Milky Way (original)', 'brand' => '#143452', 'accent' => '#e56717', 'canvas' => '#fcfbf7'],
        'forest' => ['label' => 'Forest', 'brand' => '#1f3d2b', 'accent' => '#b7791f', 'canvas' => '#faf8f1'],
        'berry' => ['label' => 'Berry', 'brand' => '#4a1d3f', 'accent' => '#da4f80', 'canvas' => '#fdf8f9'],
        'ocean' => ['label' => 'Ocean', 'brand' => '#0b3b4f', 'accent' => '#1f9aa0', 'canvas' => '#f5fafb'],
        'charcoal' => ['label' => 'Charcoal and gold', 'brand' => '#232323', 'accent' => '#a8761e', 'canvas' => '#faf9f6'],
        'terracotta' => ['label' => 'Terracotta', 'brand' => '#3b2a20', 'accent' => '#d0602f', 'canvas' => '#fbf6f0'],
    ],

];
