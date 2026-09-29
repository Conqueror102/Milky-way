@php
    $seo = config('milkyway.seo');

    $business = [
        '@type' => ['Store', 'HealthAndBeautyBusiness'],
        '@id' => $siteUrl.'/#business',
        'name' => config('app.name'),
        'description' => $metaDescription,
        'url' => $siteUrl.'/',
        'logo' => $siteUrl.'/images/logo.png',
        'image' => [$ogImage],
        'telephone' => config('milkyway.phone_dial'),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => config('milkyway.address.line'),
            'addressLocality' => config('milkyway.address.locality'),
            'addressRegion' => config('milkyway.address.region'),
            'addressCountry' => config('milkyway.address.country'),
        ],
        'openingHoursSpecification' => [[
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'opens' => '00:00',
            'closes' => '23:59',
        ]],
        'areaServed' => \App\Models\DeliveryArea::query()->ordered()->pluck('name')->all(),
        'hasMap' => 'https://www.google.com/maps/search/?api=1&query='.rawurlencode(config('milkyway.map_query')),
        // The social profiles shown in the footer; a WhatsApp chat link isn't a profile.
        'sameAs' => collect(app(\App\Support\Socials::class)->visible())->reject(fn (array $social) => $social['key'] === 'whatsapp')->pluck('url')->all(),
    ];

    $graph = [
        '@context' => 'https://schema.org',
        '@graph' => [
            $business,
            [
                '@type' => 'WebSite',
                '@id' => $siteUrl.'/#website',
                'url' => $siteUrl.'/',
                'name' => config('app.name'),
                'description' => $metaDescription,
                'inLanguage' => 'en-NG',
                'publisher' => ['@id' => $siteUrl.'/#business'],
            ],
        ],
    ];
@endphp

<meta name="description" content="{{ $metaDescription }}" />
<link rel="canonical" href="{{ $canonical }}" />

{{-- Open Graph: WhatsApp, Facebook, LinkedIn and most messengers read these --}}
<meta property="og:type" content="website" />
<meta property="og:site_name" content="{{ config('app.name') }}" />
<meta property="og:locale" content="{{ $seo['locale'] }}" />
<meta property="og:title" content="{{ $documentTitle }}" />
<meta property="og:description" content="{{ $metaDescription }}" />
<meta property="og:url" content="{{ $canonical }}" />
<meta property="og:image" content="{{ $ogImage }}" />
<meta property="og:image:type" content="image/jpeg" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:image:alt" content="{{ $seo['image_alt'] }}" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $documentTitle }}" />
<meta name="twitter:description" content="{{ $metaDescription }}" />
<meta name="twitter:image" content="{{ $ogImage }}" />
<meta name="twitter:image:alt" content="{{ $seo['image_alt'] }}" />

<script type="application/ld+json">{!! json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
