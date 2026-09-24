@props([
    'title' => null,
    'description' => null,
])

@php
    $seo = config('milkyway.seo');
    $siteUrl = rtrim(config('app.url'), '/');

    $documentTitle = filled($title) ? $title.' | '.config('app.name') : $seo['title'];
    $metaDescription = $description ?? $seo['description'];
    $canonical = $siteUrl.request()->getPathInfo();
    $ogImage = $siteUrl.$seo['image'];
    $robots = 'index, follow, max-image-preview:large';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @include('partials.seo')

        {{-- The hero photograph is the largest thing above the fold, so start fetching it at once --}}
        <link rel="preload" as="image" type="image/webp" href="/images/hero/slide-1.webp" imagesrcset="/images/hero/slide-1-640.webp 640w, /images/hero/slide-1.webp 1000w" imagesizes="(min-width: 1024px) 40rem, 100vw" fetchpriority="high" />

        {{-- Sets .motion-ready before first paint so revealed elements never flash. If the
             script that reveals them has not arrived within 4s (slow connection), it steps
             aside and everything simply shows. --}}
        <script>
            if (!matchMedia('(prefers-reduced-motion: reduce)').matches) {
                document.documentElement.classList.add('motion-ready');
                setTimeout(function () {
                    if (!window.__revealReady) document.documentElement.classList.remove('motion-ready');
                }, 4000);
            }
        </script>
    </head>
    <body class="min-h-screen bg-canvas font-sans text-cosmic-900 antialiased flex flex-col justify-between">
        <x-site.header />

        <main class="flex-1 overflow-x-clip">
            {{ $slot }}
        </main>

        <x-site.footer />

        @fluxScripts
    </body>
</html>
