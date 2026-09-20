@props([
    'message' => null,
])

@php
    $href = 'https://wa.me/'.config('milkyway.whatsapp.number')
        .'?text='.rawurlencode($message ?? config('milkyway.whatsapp.default_message'));
@endphp

<a href="{{ $href }}" target="_blank" rel="noopener" {{ $attributes }}>
    {{ $slot }}
</a>
