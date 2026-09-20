@props([
    'name',
])

{{--
    Inlines a Line Awesome icon (Icons8, MIT / Good Boy License) from resources/svg so it
    inherits colour via currentColor and costs no network request.

    To add one, copy it out of the dev dependency:
    cp node_modules/line-awesome/svg/<name>.svg resources/svg/
--}}

@php
    $file = resource_path("svg/{$name}.svg");

    throw_unless(is_file($file), InvalidArgumentException::class, "Icon [{$name}] is not in resources/svg.");

    $attributes = $attributes->class('inline-block size-5 shrink-0');

    $svg = preg_replace(
        '/<svg\b/',
        '<svg fill="currentColor" aria-hidden="true" '.$attributes->toHtml(),
        file_get_contents($file),
        1
    );
@endphp

{!! $svg !!}
