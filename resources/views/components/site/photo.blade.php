@props([
    'src',
    'alt' => '',
    'sizes' => '100vw',
    'widths' => [640, 1000, 1600],
])

{{-- A photo an admin uploaded. Cloudinary resizes it to each width on request, so
     phones get a small file just as they do for the bundled photos. --}}
<img
    src="{{ \App\Support\SiteContent::resized($src, max($widths)) }}"
    @if (\App\Support\SiteContent::resized($src, 1) !== $src)
        srcset="{{ collect($widths)->map(fn ($w) => \App\Support\SiteContent::resized($src, $w).' '.$w.'w')->implode(', ') }}"
        sizes="{{ $sizes }}"
    @endif
    alt="{{ $alt }}"
    {{ $attributes }}
/>
