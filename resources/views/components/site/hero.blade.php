@php
    $assurances = [
        ['icon' => 'store-alt-solid', 'label' => 'Wholesale & Retail'],
        ['icon' => 'truck-solid', 'label' => 'Delivery Available'],
        ['icon' => 'clock-solid', 'label' => 'Open 24 Hours'],
    ];
@endphp

{{-- Locked to the viewport: the section is exactly one screen tall and nothing spills
     past the fold. The headline is capped against vh as well as vw so it steps down on
     short windows, and the rhythm tightens further on short phones. --}}
<section id="top" class="relative isolate flex h-[100svh] flex-col justify-end overflow-hidden bg-sand lg:justify-center">

    {{-- Photograph --}}
    <picture>
        <source
            type="image/webp"
            srcset="/images/hero-model-1200.webp 1200w, /images/hero-model-1920.webp 1920w"
            sizes="100vw"
        />
        <img
            src="/images/hero-model-1920.jpg"
            srcset="/images/hero-model-1200.jpg 1200w, /images/hero-model-1920.jpg 1920w"
            sizes="(max-width: 639px) 170vw, 100vw"
            alt="Close-up of a model wearing a deep blue cosmetic cream beneath her eye"
            fetchpriority="high"
            data-enter="zoom"
            class="absolute top-0 right-0 -z-20 h-auto w-[170%] max-w-none sm:inset-0 sm:size-full sm:max-w-full sm:object-cover sm:object-[68%_top] lg:object-top"
        />
    </picture>

    {{-- Slanted fade below lg: the photograph carries on down the left while the copy
         still lands on solid sand. --}}
    <div class="absolute inset-0 -z-10 hidden bg-[linear-gradient(210deg,transparent_0%,transparent_18%,var(--color-sand)_38%)] sm:block lg:hidden"></div>

    {{-- Phones: sized to the photo itself (127vw tall), sliding down and to the right so
         the eye, cream and lips stay clear while the text side lands on solid sand. --}}
    <div class="absolute inset-x-0 top-0 -z-10 h-[127vw] bg-[linear-gradient(232deg,transparent_0%,transparent_34%,var(--color-sand)_56%)] sm:hidden"></div>

    {{-- Desktop scrim, tinted with the photograph's own beige so the blend is invisible --}}
    <div class="absolute inset-0 -z-10 hidden bg-gradient-to-r from-sand from-30% via-sand/90 via-46% to-transparent to-70% lg:block"></div>

    <div class="mx-auto w-full max-w-7xl px-6 pt-24 pb-7 max-lg:[@media(max-height:700px)]:pt-12 sm:px-8 sm:pb-9 lg:pt-24 lg:pb-8">
        <div class="max-w-xl lg:max-w-3xl">

            {{-- Eyebrow --}}
            <div data-enter style="--d:120" class="flex items-center gap-3">
                <span class="h-px w-6 bg-cosmic-900/35 sm:w-10"></span>
                <span class="font-sub text-[0.55rem] font-medium tracking-[0.1em] text-cosmic-900/70 uppercase sm:text-[0.68rem] sm:tracking-[0.32em]">
                    Skincare &middot; Beauty &middot; Body &middot; Spa
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

            {{-- Phones: everything from the paragraph down sits on a sand ground that fades in
                 above it, so the photograph can run lower without ever sitting under text. --}}
            <div class="max-sm:relative max-sm:isolate max-sm:before:absolute max-sm:before:-inset-x-6 max-sm:before:-top-10 max-sm:before:-bottom-9 max-sm:before:-z-10 max-sm:before:bg-[linear-gradient(to_bottom,transparent,var(--color-sand)_3.25rem)]">
                {{-- Supporting copy --}}
                <p data-enter style="--d:600" class="font-sub mt-6 max-lg:[@media(max-height:700px)]:mt-4 max-w-lg text-[0.95rem] leading-relaxed text-cosmic-900/75 sm:text-base lg:text-lg">
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
                <ul data-enter style="--d:860" class="mt-7 max-lg:[@media(max-height:700px)]:mt-5 max-lg:[@media(max-height:700px)]:hidden flex flex-wrap items-center gap-2">
                    @foreach ($assurances as $assurance)
                        <li class="liquid-glass font-sub inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-medium text-cosmic-900/85 ring-1 ring-gold-400/30">
                            <x-icon :name="$assurance['icon']" class="size-4 text-gold-700" />
                            {{ $assurance['label'] }}
                        </li>
                    @endforeach
                </ul>

                {{-- Location marker --}}
                <p data-enter style="--d:960" class="font-sub mt-6 inline-flex items-center gap-2 [@media(max-height:700px)]:hidden text-xs tracking-[0.12em] text-cosmic-900/55 uppercase">
                    <x-icon name="map-marker-alt-solid" class="size-4" />
                    {{ config('milkyway.address.area') }}
                </p>
            </div>
        </div>
    </div>
</section>
