@php
    $reasons = [
        [
            'icon' => 'certificate-solid',
            'title' => $site->text('why.reason_1_title'),
            'description' => $site->text('why.reason_1_text'),
        ],
        [
            'icon' => 'boxes-solid',
            'title' => $site->text('why.reason_2_title'),
            'description' => $site->text('why.reason_2_text'),
        ],
        [
            'icon' => 'layer-group-solid',
            'title' => $site->text('why.reason_3_title'),
            'description' => $site->text('why.reason_3_text'),
        ],
        [
            'icon' => 'map-marker-alt-solid',
            'title' => $site->text('why.reason_4_title'),
            'description' => $site->text('why.reason_4_text'),
        ],
        [
            'icon' => 'clock-solid',
            'title' => $site->text('why.reason_5_title'),
            'description' => $site->text('why.reason_5_text'),
        ],
        [
            'icon' => 'headset-solid',
            'title' => $site->text('why.reason_6_title'),
            'description' => $site->text('why.reason_6_text'),
        ],
    ];

    // Plates 2 and 3 are the same real photos the hero carousel uses. The crop is
    // per-photo, since a 4:3 window over a tall portrait otherwise lands on the chin.
    $plates = [
        ['field' => 'why.photo_1', 'src' => '/images/showcase/face-roller', 'pos' => 'object-center'],
        ['field' => 'why.photo_2', 'src' => '/images/hero/slide-1', 'pos' => 'object-[center_15%]'],
        ['field' => 'why.photo_3', 'src' => '/images/hero/slide-2', 'pos' => 'object-[65%_center]'],
    ];

    // An uploaded photo takes its plate's place, cropped from the centre.
    $plates = array_map(fn ($plate) => [
        'custom' => $site->image($plate['field']),
        'alt' => $site->alt($plate['field']),
    ] + $plate, $plates);
@endphp

<section id="why" class="bg-canvas py-10 lg:py-14">
    <div class="mx-auto max-w-7xl px-6 sm:px-8">

        {{-- Section label --}}
        <div class="max-w-2xl">
            <div data-reveal class="flex items-center gap-3">
                <span class="h-px w-10 bg-gold-500/60"></span>
                <span class="font-sub text-[0.68rem] font-medium tracking-[0.32em] text-gold-700 uppercase">
                    {{ $site->text('why.eyebrow') }}
                </span>
            </div>

            <h2 data-reveal="lines" style="--d:120" class="mt-4 text-[clamp(1.85rem,3.2vw,2.85rem)] leading-[1.1] font-bold tracking-[-0.01em] text-cosmic-900">
                {{ $site->text('why.heading') }}
                <span class="font-script text-[1.15em] leading-[0.8] tracking-[-0.03em] text-gold-700">{{ $site->text('why.heading_accent') }}</span>
            </h2>

            <p data-reveal style="--d:300" class="font-sub mt-4 text-base leading-relaxed text-cosmic-900/60 lg:text-lg">
                {{ $site->text('why.intro') }}
            </p>
        </div>

        <div class="mt-10 grid gap-10 lg:mt-12 lg:grid-cols-[auto_minmax(0,1fr)_minmax(0,17rem)] lg:gap-14">

            {{-- The wordmark, set vertically as the section's spine --}}
            <div class="hidden lg:flex lg:items-center lg:justify-center lg:gap-3" aria-hidden="true">
                <span
                    data-reveal="left" style="--d:200" class="block whitespace-nowrap text-[clamp(5rem,11vw,10rem)] leading-[0.85] font-bold tracking-[-0.03em] text-cosmic-900 [writing-mode:vertical-rl] rotate-180"
                >Milkyway</span>
                <span
                    data-reveal="left" style="--d:260" class="font-sub block whitespace-nowrap text-[clamp(0.65rem,1vw,0.85rem)] font-semibold tracking-[0.3em] text-cosmic-900/45 uppercase [writing-mode:vertical-rl] rotate-180"
                >Cosmetics Stores</span>
            </div>

            {{-- Plates, first on small screens so the section opens on an image --}}
            <div data-stagger="150" data-stagger-from="200" class="order-first grid grid-cols-3 gap-3 lg:order-none lg:col-start-3 lg:row-start-1 lg:flex lg:flex-col lg:gap-4">
                @foreach ($plates as $plate)
                    @if ($plate['custom'])
                        <x-site.photo
                            :src="$plate['custom']"
                            :alt="$plate['alt']"
                            sizes="(min-width: 1024px) 17rem, 33vw"
                            :widths="[400, 800]"
                            loading="lazy"
                            data-reveal="clip"
                            class="aspect-square w-full rounded-xl object-cover object-center lg:aspect-[4/3]"
                        />
                        @continue
                    @endif
                    <picture>
                        <source type="image/webp" srcset="{{ $plate['src'] }}.webp" />
                        <img
                            src="{{ $plate['src'] }}.jpg"
                            alt="{{ $plate['alt'] }}"
                            loading="lazy"
                            data-reveal="clip" class="aspect-square w-full rounded-xl object-cover {{ $plate['pos'] }} lg:aspect-[4/3]"
                        />
                    </picture>
                @endforeach
            </div>

            {{-- The reasons --}}
            <div data-stagger="120" data-stagger-from="150" class="flex flex-col gap-9 lg:col-start-2 lg:row-start-1 lg:gap-11">
                @foreach ($reasons as $reason)
                    <div data-reveal class="flex gap-5 sm:gap-6">
                        <span
                            aria-hidden="true"
                            class="shrink-0 text-[clamp(3.25rem,5.5vw,5rem)] leading-[0.75] font-bold text-cosmic-900/15 tabular-nums"
                        >{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

                        <div class="flex-1 pt-1">
                            <div class="flex items-center gap-2.5">
                                <x-icon :name="$reason['icon']" class="size-6 shrink-0 text-gold-700" />
                                <h3 class="font-sub text-sm font-bold tracking-[0.18em] text-cosmic-900 uppercase">
                                    {{ $reason['title'] }}
                                </h3>
                            </div>
                            <p class="font-sub mt-3 max-w-xl text-base leading-[1.8] text-cosmic-900/60 lg:text-[1.05rem]">
                                {{ $reason['description'] }}
                            </p>
                        </div>
                    </div>
                @endforeach

                <x-site.whatsapp-link
                    data-reveal
                    class="font-sub mt-1 inline-flex items-center justify-center gap-2.5 self-start rounded-full border border-cosmic-900/35 px-9 py-4 text-xs font-semibold tracking-[0.2em] text-cosmic-900 uppercase transition duration-200 hover:border-cosmic-900 hover:bg-cosmic-900 hover:text-cream-50 lg:self-end"
                >
                    {{ $site->text('why.button') }}
                </x-site.whatsapp-link>
            </div>
        </div>
    </div>
</section>
