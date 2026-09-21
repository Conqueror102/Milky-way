@props([
    'title' => null,
    'description' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')

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

        @if ($description)
            <meta name="description" content="{{ $description }}" />
            <meta property="og:description" content="{{ $description }}" />
        @endif
        <meta property="og:title" content="{{ $title ? $title.' - '.config('app.name') : config('app.name') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{ url('/images/hero-model-1200.jpg') }}" />
        <meta name="twitter:card" content="summary_large_image" />
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
