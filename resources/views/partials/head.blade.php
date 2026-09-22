<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ $documentTitle ?? (filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel')) }}
</title>

{{-- Everything is kept out of search results unless a layout opts in by setting $robots --}}
<meta name="robots" content="{{ $robots ?? 'noindex, nofollow' }}" />

@include('partials.favicons')

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
