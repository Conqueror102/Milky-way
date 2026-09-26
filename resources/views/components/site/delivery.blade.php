@php
    /**
     * Each destination sits further out on its own orbit, so distance from the shop
     * reads as distance on the page. Positions are percentages of the diagram box.
     *
     * @var array<int, array{label: string, left: float, top: float}>
     */
    $orbits = [
        ['left' => 22.8, 'top' => 72.9],
        ['left' => 36.2, 'top' => 57.9],
        ['left' => 49.6, 'top' => 42.9],
        ['left' => 63.0, 'top' => 27.9],
    ];

    $destinations = collect(config('milkyway.delivery_areas'))
        ->take(count($orbits))
        ->values()
        ->map(fn ($label, $i) => $orbits[$i] + ['label' => $label]);
@endphp

<section id="delivery" class="bg-cosmic-950 py-10 lg:py-14">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-6 sm:px-8 lg:grid-cols-2 lg:gap-16">

        {{-- The ask --}}
        <div>
            <div data-reveal class="flex items-center gap-3">
                <span class="h-px w-10 bg-gold-400/50"></span>
                <span class="font-sub text-[0.68rem] font-medium tracking-[0.32em] text-gold-400 uppercase">
                    {{ $site->text('delivery.eyebrow') }}
                </span>
            </div>

            <h2 data-reveal="lines" style="--d:120" class="mt-4 text-[clamp(1.85rem,3.2vw,2.85rem)] leading-[1.1] font-bold tracking-[-0.01em] text-cream-50">
                {{ $site->text('delivery.heading') }}
                <span class="font-script text-[1.15em] leading-[0.8] tracking-[-0.03em] text-gold-400">{{ $site->text('delivery.heading_accent') }}</span>
            </h2>

            <p data-reveal style="--d:300" class="font-sub mt-5 max-w-md text-base leading-relaxed text-cream-50/65 lg:text-lg">
                {{ $site->text('delivery.intro') }}
            </p>

            <x-site.whatsapp-link
                data-reveal
                style="--d:450"
                message="Hello Milkyway Cosmetics Stores. I would like to ask about delivery to [your area]."
                class="font-sub mt-8 inline-flex items-center justify-center gap-3 rounded-full bg-cream-50 px-8 py-4 text-sm font-semibold text-cosmic-900 transition duration-200 hover:bg-white"
            >
                <x-icon name="whatsapp" class="size-5 text-cosmic-900" />
                {{ $site->text('delivery.button') }}
            </x-site.whatsapp-link>
        </div>

        {{-- Reach, drawn as orbits out from the shop --}}
        <div data-reveal="trigger" data-orbits class="relative aspect-[4/3] w-full">
            <svg class="absolute inset-0 size-full" viewBox="0 0 400 300" fill="none" aria-hidden="true">
                @foreach ([80, 150, 220, 290] as $radius)
                    <circle cx="30" cy="270" r="{{ $radius }}"
                        stroke="currentColor" stroke-width="1" stroke-dasharray="4 6"
                        class="orbit-ring text-gold-400/35" style="--i: {{ $loop->index }}" />
                @endforeach
                <circle cx="30" cy="270" r="16" class="orbit-core text-gold-400/20" fill="currentColor" />
                <circle cx="30" cy="270" r="6" class="orbit-core text-gold-400" fill="currentColor" />
            </svg>

            <p class="font-sub absolute top-[90%] left-[13%] -translate-y-1/2 text-[0.62rem] leading-snug tracking-[0.16em] whitespace-nowrap text-cream-50/50 uppercase">
                {{ $site->text('delivery.shop_label') }}
            </p>

            @foreach ($destinations as $destination)
                <span
                    class="orbit-pill font-sub absolute -translate-x-1/2 -translate-y-1/2 rounded-full bg-cream-50 px-3.5 py-1.5 text-xs font-semibold whitespace-nowrap text-cosmic-900 shadow-lg shadow-cosmic-950/40"
                    style="left: {{ $destination['left'] }}%; top: {{ $destination['top'] }}%; --i: {{ $loop->index }}"
                >{{ $destination['label'] }}</span>
            @endforeach

            <span class="font-sub absolute top-[6%] right-0 rounded-full border border-gold-400/40 px-3.5 py-1.5 text-xs font-medium text-gold-400">
                {{ $site->text('delivery.other_label') }}
            </span>
        </div>
    </div>
</section>
