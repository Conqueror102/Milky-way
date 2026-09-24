@php
    $assurances = [
        ['icon' => 'store-alt-solid', 'label' => 'Wholesale & Retail'],
        ['icon' => 'truck-solid', 'label' => 'Delivery Available'],
        ['icon' => 'clock-solid', 'label' => 'Open 24 Hours'],
    ];

    // Lifestyle photos, cross-fading full-bleed behind the copy, the same way the
    // single photo used to. Each has its own background tone (mustard, grey, neutral)
    // so there is no one colour that blends invisibly with all of them -- the fades
    // below lean on the brand orange instead.
    //
    // Sharpness is set by how far a photo is stretched past its native size. The sources
    // are only ~500px wide, so on desktop the photo is capped at 40rem (about 1.3x native)
    // and its own left edge dissolves into the orange, rather than being stretched across
    // the viewport. 'natural' keeps a landscape photo at its own aspect ratio: forcing it
    // to fill the full height would scale it ~2.3x, which is what made it so soft.
    $fits = [
        'cover' => 'lg:h-full lg:w-[min(58%,40rem)] lg:[mask-image:linear-gradient(to_right,transparent_0%,black_32%)]',
        'natural' => 'lg:top-1/2 lg:h-auto lg:w-[min(58%,40rem)] lg:-translate-y-1/2 lg:[mask-image:linear-gradient(to_right,transparent_0%,black_32%),linear-gradient(to_bottom,transparent_0%,black_20%,black_80%,transparent_100%)] lg:[mask-composite:intersect]',
    ];

    // Below lg the photo is its own frame across the top of the screen with the copy
    // underneath, fading into the orange along its bottom edge. Full-bleed behind the
    // copy only ever showed a random slice of a face.
    $frame = 'absolute inset-x-0 top-0 h-[46svh] w-full max-w-none object-cover [mask-image:linear-gradient(to_bottom,black_62%,transparent_100%)] sm:h-[50svh] lg:inset-x-auto lg:right-0';

    $slides = [
        ['key' => 'slide-1', 'alt' => 'A woman smiling while applying raw shea butter to her face', 'pos' => 'object-[center_25%]', 'fit' => 'cover'],
        ['key' => 'slide-2', 'alt' => 'A woman checking a hand mirror while applying skincare cream', 'pos' => 'object-[35%_30%]', 'fit' => 'natural'],
        // slide-3 (towel + flowers) is dropped: it's almost entirely pale robe and white
        // towel, so against the orange wash it renders as a near-blank rectangle no
        // matter the crop -- not a framing problem, the photo itself doesn't work here.
        ['key' => 'slide-4', 'alt' => 'Close-up of a woman applying a dollop of cream to her cheek', 'pos' => 'object-[center_32%]', 'fit' => 'cover'],
    ];
@endphp

{{-- Locked to the viewport: the section is exactly one screen tall and nothing spills
     past the fold. The headline is capped against vh as well as vw so it steps down on
     short windows, and the rhythm tightens further on short phones. --}}
<section
    id="top"
    x-data="{
        slide: 0,
        total: {{ count($slides) }},
        timer: null,
        play() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            this.timer = setInterval(() => this.slide = (this.slide + 1) % this.total, 4000);
        },
        pause() { clearInterval(this.timer); },
    }"
    x-init="play()"
    class="relative isolate flex h-[100svh] flex-col justify-end overflow-hidden bg-sand lg:justify-center"
>

    {{-- Photographs, cross-fading. One <picture> per photo: the first is fetched at high
         priority and matches the preload in the layout, so it is downloaded once and paints
         at once; the rest start hidden and load quietly behind it. --}}
    @foreach ($slides as $i => $item)
        <picture>
            <source type="image/webp" srcset="/images/hero/{{ $item['key'] }}.webp" />
            <img
                src="/images/hero/{{ $item['key'] }}.jpg"
                alt="{{ $item['alt'] }}"
                @if ($i === 0)
                    fetchpriority="high"
                @else
                    fetchpriority="low"
                @endif
                :class="slide === {{ $i }} ? 'opacity-100!' : 'opacity-0'"
                class="{{ $frame }} -z-20 {{ $item['pos'] }} {{ $fits[$item['fit']] }} {{ $i === 0 ? '' : 'opacity-0' }} transition-opacity duration-1000 ease-out"
            />
        </picture>
    @endforeach

    {{-- A soft brand-orange wash ties the four different photo backgrounds together --}}
    <div class="absolute inset-0 -z-10 bg-sand/15"></div>

    {{-- Desktop scrim --}}
    <div class="absolute inset-0 -z-10 hidden bg-gradient-to-r from-sand from-30% via-sand/90 via-46% to-transparent to-70% lg:block"></div>

    {{-- Dots --}}
    <div class="absolute right-6 bottom-6 z-10 hidden gap-1.5 sm:flex lg:right-10 lg:bottom-10">
        @foreach ($slides as $i => $item)
            <button
                type="button"
                x-on:click="slide = {{ $i }}; pause(); play()"
                :class="slide === {{ $i }} ? 'w-6 bg-cosmic-900' : 'w-1.5 bg-cosmic-900/30 hover:bg-cosmic-900/55'"
                class="h-1.5 rounded-full transition-all duration-300"
            ><span class="sr-only">Show photo {{ $i + 1 }}</span></button>
        @endforeach
    </div>

    <div class="mx-auto w-full max-w-7xl px-6 pt-24 pb-7 max-lg:[@media(max-height:700px)]:pt-12 sm:px-8 sm:pb-9 lg:pt-24 lg:pb-8">
        <div class="max-w-xl lg:max-w-3xl">

            {{-- Eyebrow --}}
            <div data-enter style="--d:120" class="flex items-center gap-3">
                <span class="h-px w-6 bg-cosmic-900/35 sm:w-10"></span>
                <span class="font-sub text-[0.55rem] font-medium tracking-[0.1em] text-cosmic-900/70 uppercase sm:text-[0.68rem] sm:tracking-[0.32em]">
                    Health/Beauty Supplements &middot; Skincare Products &middot; Body Enhancer
                </span>
            </div>

            {{-- Headline --}}
            <h1 class="mt-5 max-lg:[@media(max-height:700px)]:mt-3 text-[clamp(2.6rem,min(7.4vw,13vh),6.25rem)] leading-[1] font-bold tracking-[-0.01em] text-cosmic-900">
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

            <div>
                {{-- Supporting copy --}}
                <p data-enter style="--d:600" class="font-sub mt-6 max-lg:[@media(max-height:700px)]:mt-4 max-w-lg text-[0.95rem] leading-relaxed text-cosmic-900/90 sm:text-base lg:text-lg">
                    Skincare, beauty, body enhancement and spa products at
                    <span class="font-semibold text-cosmic-900">wholesale and retail prices</span>.
                    Shopping for yourself, or stocking your beauty business — we've got you covered.
                </p>

                {{-- Calls to action --}}
                <div data-enter style="--d:740" class="mt-7 max-lg:[@media(max-height:700px)]:mt-5 flex flex-col gap-2.5 sm:flex-row sm:items-center sm:gap-3">
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
                <ul data-enter style="--d:860" class="hidden sm:flex mt-7 max-lg:[@media(max-height:700px)]:mt-5 max-lg:[@media(max-height:700px)]:hidden flex-wrap items-center gap-2">
                    @foreach ($assurances as $assurance)
                        <li class="liquid-glass font-sub inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-medium text-cosmic-900/85 ring-1 ring-gold-400/30">
                            <x-icon :name="$assurance['icon']" class="size-4 text-gold-700" />
                            {{ $assurance['label'] }}
                        </li>
                    @endforeach
                </ul>

                {{-- Location marker --}}
                <p data-enter style="--d:960" class="font-sub mt-6 hidden sm:inline-flex items-center gap-2 [@media(max-height:700px)]:hidden text-xs tracking-[0.12em] text-cosmic-900/55 uppercase">
                    <x-icon name="map-marker-alt-solid" class="size-4" />
                    {{ config('milkyway.address.area') }}
                </p>
            </div>
        </div>
    </div>
</section>
