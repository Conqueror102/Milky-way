@php
    /**
     * Each pill is pinned to its own branch of the artwork. Positions are percentages of
     * the image box, and the image keeps its natural aspect ratio, so they hold at every
     * width. `x`/`y` are the pill's centre.
     *
     * @var array<int, array{label: string, x: float, y: float}>
     */
    $branches = [
        ['label' => $site->text('audience.label_1'), 'x' => 13, 'y' => 31],
        ['label' => $site->text('audience.label_2'), 'x' => 50, 'y' => 13],
        ['label' => $site->text('audience.label_3'), 'x' => 86, 'y' => 20],
        ['label' => $site->text('audience.label_4'), 'x' => 10, 'y' => 64],
        ['label' => $site->text('audience.label_5'), 'x' => 90, 'y' => 64],
        ['label' => $site->text('audience.label_6'), 'x' => 21, 'y' => 89],
        ['label' => $site->text('audience.label_7'), 'x' => 50, 'y' => 94],
        ['label' => $site->text('audience.label_8'), 'x' => 78, 'y' => 89],
    ];
@endphp

{{-- The artwork is the section. Its canvas was extended in its own navy so the floating
     header has room to sit over it without covering the composition. --}}
<section id="who" class="relative isolate bg-deep">
    {{-- Heading, in the headroom above the composition --}}
    <div class="relative px-6 pt-24 pb-4 sm:px-8 lg:absolute lg:inset-x-0 lg:top-0 lg:pb-0 lg:pt-28">
        <div class="mx-auto max-w-7xl">
            <div data-reveal class="flex items-center gap-3">
                <span class="h-px w-10 bg-gold-400/50"></span>
                <span class="font-sub text-[0.68rem] font-medium tracking-[0.32em] text-gold-400 uppercase">
                    {{ $site->text('audience.eyebrow') }}
                </span>
            </div>
            <h2 data-reveal="lines" style="--d:120" class="mt-3 text-[clamp(1.6rem,2.6vw,2.5rem)] leading-[1.1] font-bold tracking-[-0.01em] text-cream-50">
                {{ $site->text('audience.heading') }}
                <span class="font-script text-[1.15em] leading-[0.8] tracking-[-0.03em] text-gold-400">{{ $site->text('audience.heading_accent') }}</span>
            </h2>
        </div>
    </div>

    <picture>
        <source media="(min-width: 1024px)" type="image/webp" srcset="/images/audience/wide-1300.webp 1300w, /images/audience/wide-1900.webp 1900w" sizes="100vw" />
        <source media="(min-width: 1024px)" type="image/jpeg" srcset="/images/audience/wide-1300.jpg 1300w, /images/audience/wide-1900.jpg 1900w" sizes="100vw" />
        <source type="image/webp" srcset="/images/audience/tall-640.webp 640w, /images/audience/tall-900.webp 900w" sizes="100vw" />
        <img
            src="/images/audience/tall-900.jpg"
            srcset="/images/audience/tall-640.jpg 640w, /images/audience/tall-900.jpg 900w"
            sizes="100vw"
            alt="The Milkyway Cosmetics mark surrounded by the people it serves: someone applying skincare, a shopper, a wholesale buyer, an entrepreneur, a reseller, a makeup artist, a spa client and a salon stylist"
            data-reveal="zoom" class="w-full"
        />
    </picture>

    {{-- Pills, pinned to their branches on lg --}}
    <ul data-stagger="110" data-stagger-from="500" class="pointer-events-none absolute inset-0 hidden lg:block">
        @foreach ($branches as $branch)
            <li
                class="absolute -translate-x-1/2 -translate-y-1/2"
                style="left: {{ $branch['x'] }}%; top: {{ $branch['y'] }}%"
            >
                <span data-reveal="pop" class="font-sub block rounded-full bg-sand px-[1.1em] py-[0.55em] text-center text-[clamp(0.78rem,1.05vw,1.15rem)] font-semibold whitespace-nowrap text-cosmic-900 shadow-lg shadow-cosmic-950/25">
                    {{ $branch['label'] }}
                </span>
            </li>
        @endforeach
    </ul>

    {{-- Below lg the artwork is too narrow to carry labels, so they list underneath --}}
    <div class="px-6 pb-12 sm:px-8 lg:hidden">
        <ul data-stagger="70" class="mx-auto flex max-w-7xl flex-wrap justify-center gap-2">
            @foreach ($branches as $branch)
                <li data-reveal class="font-sub rounded-full bg-sand px-3.5 py-2 text-xs font-semibold text-cosmic-900 sm:px-4 sm:py-2.5 sm:text-sm">
                    {{ $branch['label'] }}
                </li>
            @endforeach
        </ul>
    </div>
</section>
