@props([
    'product',
    'loading' => 'lazy',
])

@if ($product->imageSrc())
    <picture>
        @if ($product->imageWebp())
            <source type="image/webp" srcset="{{ $product->imageWebp() }}" />
        @endif
        <img src="{{ $product->imageSrc() }}" alt="{{ $product->name }}" loading="{{ $loading }}" {{ $attributes->class('object-cover') }} />
    </picture>
@else
    <div {{ $attributes->class('grid place-items-center bg-cosmic-900/6 text-cosmic-900/30') }}>
        <x-icon name="shopping-bag-solid" class="size-10" />
    </div>
@endif
