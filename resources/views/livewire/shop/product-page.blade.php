<div class="mx-auto max-w-7xl px-6 pt-28 pb-16 sm:px-8 lg:pt-36 lg:pb-24">
    <a href="{{ route('home') }}#shop" class="font-sub inline-flex items-center gap-2 text-sm font-medium text-cosmic-900/65 transition hover:text-cosmic-900">
        <x-icon name="arrow-left-solid" class="size-4" />
        Back to the shop
    </a>

    <div class="mt-6 grid gap-8 lg:grid-cols-2 lg:gap-14">
        @php $gallery = $product->gallery(); @endphp

        <div class="rounded-[2rem] bg-white p-3 shadow-[0_12px_32px_-14px_color-mix(in_srgb,var(--color-cosmic-900)_30%,transparent)] ring-1 ring-cosmic-900/8">
            @if ($gallery !== [])
                {{-- Main photo, plus thumbnails when there are several; the first photo is server-rendered so it shows without JS. --}}
                <div x-data="{ active: 0 }" wire:ignore>
                    <div class="relative">
                        @foreach ($gallery as $index => $image)
                            <picture
                                @if ($index > 0) x-cloak @endif
                                x-show="active === {{ $index }}"
                            >
                                @if ($image['webp'])
                                    <source type="image/webp" srcset="{{ $image['webp'] }}" />
                                @endif
                                <img
                                    src="{{ $image['src'] }}"
                                    alt="{{ $image['alt'] }}"
                                    loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                    class="aspect-square w-full rounded-[1.5rem] object-cover"
                                />
                            </picture>
                        @endforeach
                    </div>

                    @if (count($gallery) > 1)
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($gallery as $index => $image)
                            <button
                                type="button"
                                x-on:click="active = {{ $index }}"
                                :class="active === {{ $index }} ? 'ring-2 ring-cosmic-900' : 'ring-1 ring-cosmic-900/10 opacity-75 hover:opacity-100'"
                                class="size-16 overflow-hidden rounded-xl transition sm:size-20"
                            >
                                <span class="sr-only">Show photo {{ $index + 1 }}</span>
                                <img src="{{ $image['src'] }}" alt="" loading="lazy" class="size-full object-cover" />
                            </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            @else
                <x-shop.product-image :product="$product" loading="eager" class="aspect-square w-full rounded-[1.5rem]" />
            @endif
        </div>

        <div class="flex flex-col">
            <div class="flex flex-wrap gap-1.5">
                <span class="font-sub rounded-full bg-cosmic-900/6 px-3 py-1.5 text-[0.7rem] font-medium text-cosmic-900/70">{{ $product->category }}</span>
                @if ($product->type)
                    <span class="font-sub rounded-full bg-cosmic-900/6 px-3 py-1.5 text-[0.7rem] font-medium text-cosmic-900/70">{{ $product->type }}</span>
                @endif
            </div>

            <h1 class="mt-4 text-[clamp(2rem,3.6vw,3rem)] leading-[1.08] font-bold tracking-[-0.01em] text-cosmic-900">{{ $product->name }}</h1>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <p class="text-2xl font-bold text-gold-600">
                    {{ $product->formattedPrice() ?? 'Price on request' }}
                </p>
                @if ($product->usesDemoPrice())
                    <span class="font-sub rounded-full bg-gold-100 px-2.5 py-1 text-[0.65rem] font-semibold text-gold-800" title="Shown on preview sites only, until a real price is set">Preview price</span>
                @endif
            </div>

            @if ($product->description)
                <p class="font-sub mt-5 max-w-prose text-base leading-relaxed text-cosmic-900/75">{{ $product->description }}</p>
            @endif

            <div class="mt-8 rounded-[1.5rem] bg-white p-5 ring-1 ring-cosmic-900/8 sm:p-6">
                @if ($product->isPurchasable())
                    <div class="flex flex-wrap items-center gap-3">
                        <x-shop.quantity-stepper :quantity="$quantity" decrement="decrement" increment="increment" />

                        <button
                            type="button"
                            wire:click="addToCart"
                            class="font-sub inline-flex flex-1 items-center justify-center gap-2 rounded-full bg-cosmic-900 px-6 py-3.5 text-sm font-bold text-white transition duration-200 hover:bg-cosmic-800 disabled:opacity-60"
                            wire:loading.attr="disabled"
                            wire:target="addToCart"
                        >
                            <x-icon name="shopping-bag-solid" class="size-4" />
                            Add to cart
                        </button>
                    </div>

                    @if ($product->stock !== null && $product->stock <= 10)
                        <p class="font-sub mt-3 text-sm font-semibold text-gold-700">Only {{ $product->stock }} left in stock</p>
                    @endif

                    @error('quantity')
                        <p class="font-sub mt-3 text-sm text-red-700">{{ $message }}</p>
                    @enderror

                    @if ($added)
                        <div class="font-sub mt-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-cosmic-900/5 px-4 py-3 text-sm text-cosmic-900" role="status">
                            <span class="inline-flex items-center gap-2 font-semibold">
                                <x-icon name="check-circle-solid" class="size-5 text-gold-600" />
                                Added to your cart
                            </span>
                            <a href="{{ route('cart') }}" class="font-bold underline underline-offset-4 hover:text-cosmic-800">View cart</a>
                        </div>
                    @endif
                @elseif ($product->isSoldOut())
                    <p class="font-sub text-base font-semibold text-cosmic-900">Sold out</p>
                    <p class="font-sub mt-1 text-sm text-cosmic-900/70">This product is out of stock at the moment. Check back soon.</p>
                @else
                    <p class="font-sub text-sm leading-relaxed text-cosmic-900/70">
                        This product isn't available to order online yet.
                    </p>
                @endif
            </div>

            <ul class="font-sub mt-6 grid gap-2 text-sm text-cosmic-900/70">
                <li class="flex items-center gap-2"><x-icon name="truck-solid" class="size-4 text-gold-600" /> Delivery to {{ implode(', ', config('milkyway.delivery_areas')) }}</li>
                <li class="flex items-center gap-2"><x-icon name="boxes-solid" class="size-4 text-gold-600" /> Retail and bulk quantities</li>
            </ul>
        </div>
    </div>

    @if ($this->related->isNotEmpty())
        <section class="mt-16 lg:mt-24">
            <h2 class="text-2xl font-bold text-cosmic-900">More in {{ $product->category }}</h2>

            <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ($this->related as $item)
                    <a href="{{ route('products.show', $item) }}" wire:key="related-{{ $item->id }}" class="group flex flex-col rounded-[1.5rem] bg-white p-3 ring-1 ring-cosmic-900/8 transition hover:ring-cosmic-900/25">
                        <x-shop.product-image :product="$item" class="aspect-square w-full rounded-[1rem]" />
                        <h3 class="mt-3 px-0.5 text-base leading-tight font-bold text-cosmic-900 group-hover:underline">{{ $item->name }}</h3>
                        <p class="font-sub mt-1 px-0.5 text-sm font-semibold text-gold-600">{{ $item->formattedPrice() ?? 'Price on request' }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>
