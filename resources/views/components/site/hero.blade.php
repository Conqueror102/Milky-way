@php
    $assurances = [
        ['icon' => 'store-alt-solid', 'label' => 'Wholesale & Retail'],
        ['icon' => 'truck-solid', 'label' => 'Delivery Available'],
        ['icon' => 'clock-solid', 'label' => 'Open 24 Hours'],
    ];

    // One real photo per category, cycling on the right. Beauty & Cosmetics has no real
    // product photo yet, so it is left out rather than standing in with a stock image.
    $showcase = [
        ['image' => '/images/categories/skincare2/cosrx', 'label' => 'Skincare'],
        ['image' => '/images/categories/health/anua', 'label' => 'Health & Beauty'],
        ['image' => '/images/categories/sexual/menpower', 'label' => 'Sexual Enhancement'],
        ['image' => '/images/categories/body2/bootybloom', 'label' => 'Body Enhancement'],
        ['image' => '/images/categories/spa2/mooyam', 'label' => 'Spa & Massage'],
        ['image' => '/images/categories/body2/beckon', 'label' => 'Wholesale'],
    ];
@endphp

<section id="top" class="relative isolate overflow-hidden bg-sand lg:flex lg:h-[100svh] lg:items-center">
    <div class="mx-auto grid w-full max-w-7xl gap-10 px-6 py-24 sm:px-8 lg:grid-cols-2 lg:items-center lg:gap-16 lg:py-0">

        {{-- Copy --}}
        <div class="max-w-xl lg:max-w-none">

            {{-- Eyebrow --}}
            <div data-enter style="--d:120" class="flex items-center gap-3">
                <span class="h-px w-6 bg-cosmic-900/35 sm:w-10"></span>
                <span class="font-sub text-[0.5rem] font-medium tracking-[0.04em] text-cosmic-900/90 uppercase sm:text-[0.65rem] sm:tracking-[0.2em]">
                    Health/Beauty Supplements &middot; Skincare Products &middot; Body Enhancer
                </span>
            </div>

            {{-- Headline --}}
            <h1 class="mt-5 text-[clamp(2.6rem,min(7.4vw,13vh),6.25rem)] leading-[1] font-bold tracking-[-0.01em] text-cosmic-900">
                <span data-enter style="--d:260" class="block">Your Beauty.</span>
                <span data-enter style="--d:420" class="mt-2 block ps-1 lg:ps-16">
                    <span class="align-baseline">Our</span>
                    <span class="relative ms-2 inline-block">
                        <span class="font-script text-[1.18em] leading-[0.8] font-normal tracking-[-0.03em] text-gold-800">Passion</span>
                        <svg viewBox="0 0 200 10" preserveAspectRatio="none" fill="none" aria-hidden="true"
                             class="absolute -bottom-[0.06em] left-0 h-[0.13em] w-full text-gold-600">
                            <path d="M3 7.4C38 2.6 77 1.4 107 2.8c24 1.1 52 3.4 90 1.1" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                        </svg>
                    </span>
                </span>
            </h1>

            {{-- Supporting copy --}}
            <p data-enter style="--d:600" class="font-sub mt-6 max-w-lg text-[0.95rem] leading-relaxed text-cosmic-900/90 sm:text-base lg:text-lg">
                Skincare, beauty, body enhancement and spa products at
                <span class="font-semibold text-cosmic-900">wholesale and retail prices</span>.
                Shopping for yourself, or stocking your beauty business — we've got you covered.
            </p>

            {{-- Calls to action --}}
            <div data-enter style="--d:740" class="mt-7 flex flex-col gap-2.5 sm:flex-row sm:items-center sm:gap-3">
                <a href="#shop" class="group inline-flex items-center justify-center font-sub gap-2.5 rounded-full bg-cosmic-900 px-7 py-4 text-sm font-semibold text-cream-50 ring-1 ring-gold-400/40 transition duration-200 hover:bg-cosmic-950 hover:ring-gold-400">
                    Shop our products
                    <x-icon name="arrow-right-solid" class="size-4 text-gold-400 transition-transform duration-200 group-hover:translate-x-0.5" />
                </a>

                <x-site.whatsapp-link class="liquid-glass font-sub inline-flex items-center justify-center gap-2.5 rounded-full px-7 py-4 text-sm font-semibold text-cosmic-900 transition duration-200 hover:bg-white/85">
                    <x-icon name="whatsapp" class="size-4 text-cosmic-900" />
                    Chat with us on WhatsApp
                </x-site.whatsapp-link>
            </div>

            {{-- Assurance pills --}}
            <ul data-enter style="--d:860" class="mt-7 flex flex-wrap items-center gap-2">
                @foreach ($assurances as $assurance)
                    <li class="liquid-glass font-sub inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-medium text-cosmic-900/85 ring-1 ring-gold-400/30">
                        <x-icon :name="$assurance['icon']" class="size-4 text-gold-700" />
                        {{ $assurance['label'] }}
                    </li>
                @endforeach
            </ul>

            {{-- Location marker --}}
            <p data-enter style="--d:960" class="font-sub mt-6 inline-flex items-center gap-2 text-xs tracking-[0.12em] text-cosmic-900/55 uppercase">
                <x-icon name="map-marker-alt-solid" class="size-4" />
                {{ config('milkyway.address.area') }}
            </p>
        </div>

        {{-- Product carousel, in place of the old photograph --}}
        <div
            data-enter="zoom"
            style="--d:200"
            x-data="{
                slide: 0,
                total: {{ count($showcase) }},
                timer: null,
                play() {
                    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                    this.timer = setInterval(() => this.slide = (this.slide + 1) % this.total, 3200);
                },
                pause() { clearInterval(this.timer); },
            }"
            x-init="play()"
            x-on:mouseenter="pause()"
            x-on:mouseleave="play()"
            class="relative aspect-[4/3] w-full overflow-hidden rounded-[1.75rem] shadow-[0_24px_60px_-20px_rgba(20,52,82,0.35)] sm:aspect-[16/10] lg:aspect-square"
        >
            {{-- Server-rendered base: the first product, and the no-JS fallback --}}
            <img
                src="{{ $showcase[0]['image'] }}.jpg"
                alt="{{ $showcase[0]['label'] }}"
                fetchpriority="high"
                class="absolute inset-0 -z-20 size-full object-cover"
            />
            @foreach ($showcase as $i => $item)
                <picture>
                    <source type="image/webp" srcset="{{ $item['image'] }}.webp" />
                    <img
                        src="{{ $item['image'] }}.jpg"
                        alt="{{ $item['label'] }}"
                        loading="lazy"
                        :class="slide === {{ $i }} ? 'opacity-100' : 'opacity-0'"
                        class="absolute inset-0 -z-10 size-full object-cover transition-opacity duration-700 ease-out"
                    />
                </picture>
            @endforeach

            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-cosmic-950/70 from-5% via-transparent via-40%"></div>

            <span
                class="font-sub absolute top-4 left-4 rounded-full bg-cosmic-950/55 px-3.5 py-1.5 text-xs font-semibold text-cream-50 backdrop-blur-sm"
                x-text="{{ Js::from(collect($showcase)->pluck('label')) }}[slide]"
            >{{ $showcase[0]['label'] }}</span>

            <div class="absolute inset-x-0 bottom-4 flex justify-center gap-1.5">
                @foreach ($showcase as $i => $item)
                    <button
                        type="button"
                        x-on:click="slide = {{ $i }}; pause(); play()"
                        :class="slide === {{ $i }} ? 'w-6 bg-cream-50' : 'w-1.5 bg-cream-50/50 hover:bg-cream-50/75'"
                        class="h-1.5 rounded-full transition-all duration-300"
                    ><span class="sr-only">Show {{ $item['label'] }}</span></button>
                @endforeach
            </div>
        </div>
    </div>
</section>
