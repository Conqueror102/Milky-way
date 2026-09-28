@props([
    'product',
])

{{-- A product in the shop grid: photo, name, price, and buttons to open it or add one to the cart. --}}
<article {{ $attributes->class('flex flex-col rounded-[1.5rem] rounded-b-[2.25rem] bg-white p-3 ring-1 ring-cosmic-900/8 shadow-[0_12px_32px_-14px_color-mix(in_srgb,var(--color-cosmic-900)_30%,transparent)]') }}>
    <a href="{{ route('products.show', $product) }}" class="relative block">
        <x-shop.product-image :product="$product" class="aspect-square w-full rounded-[1rem]" />
        <span class="font-sub absolute top-2.5 left-2.5 rounded-full bg-cosmic-950/55 px-2.5 py-1 text-[0.6rem] font-semibold text-white backdrop-blur-sm">
            {{ $site->text('categories.card_badge') }}
        </span>
    </a>

    <h3 class="mt-4 px-0.5 text-base leading-tight font-bold text-cosmic-900">
        <a href="{{ route('products.show', $product) }}" class="hover:underline">{{ $product->name }}</a>
    </h3>

    <p class="font-sub mt-1 px-0.5 text-sm font-semibold text-gold-600">{{ $product->formattedPrice() ?? 'Price on request' }}</p>

    <p class="font-sub mt-2 mb-4 line-clamp-2 px-0.5 text-xs leading-relaxed text-cosmic-900/55">
        {{ $product->description }}
    </p>

    <div class="mt-auto flex items-center gap-2">
        <a
            href="{{ route('products.show', $product) }}"
            class="font-sub block min-w-0 flex-1 rounded-full bg-cosmic-900 py-3.5 text-center text-sm font-bold text-white transition duration-200 hover:bg-cosmic-800"
        >{{ $site->text('categories.product_button') }}</a>

        @if ($product->isPurchasable())
            <livewire:shop.card-add-to-cart :product="$product" :key="'card-cart-'.$product->id" />
        @endif
    </div>
</article>
