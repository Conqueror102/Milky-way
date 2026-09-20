@php
    $reasons = [
        [
            'icon' => 'certificate-solid',
            'title' => 'Guaranteed Quality',
            'description' => '100% authentic skincare, beauty, and spa formulations.',
        ],
        [
            'icon' => 'boxes-solid',
            'title' => 'Wholesale & Retail',
            'description' => 'Buy single units for yourself or bulk cartons for your business.',
        ],
        [
            'icon' => 'layer-group-solid',
            'title' => 'Wide Variety',
            'description' => 'Everything from daily cleansers and makeup to professional spa oils and wellness.',
        ],
        [
            'icon' => 'map-marker-alt-solid',
            'title' => 'Prime Location',
            'description' => 'Easily accessible at Bornu Plaza, Tradefair Complex, Lagos.',
        ],
        [
            'icon' => 'clock-solid',
            'title' => '24/7 Availability',
            'description' => 'Order, enquire, and check stock anytime via WhatsApp.',
        ],
        [
            'icon' => 'headset-solid',
            'title' => 'Dedicated Customer Support',
            'description' => 'Fast responses, product guidance, and reliable service.',
        ],
    ];

    $plates = [
        ['key' => 'face-roller', 'alt' => 'A woman using a rose quartz face roller'],
        ['key' => 'podium', 'alt' => 'Cosmetic bottles and tubes arranged on a display podium'],
        ['key' => 'spa-massage', 'alt' => 'A woman receiving an oil massage in a spa'],
    ];
@endphp

<section id="why" class="bg-canvas py-10 lg:py-14">
    <div class="mx-auto max-w-7xl px-6 sm:px-8">

        {{-- Section label --}}
        <div class="max-w-2xl">
            <div class="flex items-center gap-3">
                <span class="h-px w-10 bg-gold-500/60"></span>
                <span class="font-sub text-[0.68rem] font-medium tracking-[0.32em] text-gold-700 uppercase">
                    Why choose us
                </span>
            </div>

            <h2 class="mt-4 text-[clamp(1.85rem,3.2vw,2.85rem)] leading-[1.1] font-bold tracking-[-0.01em] text-cosmic-900">
                Quality beauty, made
                <span class="font-script text-[1.15em] leading-[0.8] tracking-[-0.03em] text-gold-700">accessible</span>
            </h2>

            <p class="font-sub mt-4 text-base leading-relaxed text-cosmic-900/60 lg:text-lg">
                For everyday shoppers and beauty businesses alike &mdash; here is what you can count on.
            </p>
        </div>

        <div class="mt-10 grid gap-10 lg:mt-12 lg:grid-cols-[auto_minmax(0,1fr)_minmax(0,17rem)] lg:gap-14">

            {{-- The wordmark, set vertically as the section's spine --}}
            <div class="hidden lg:flex lg:items-center lg:justify-center">
                <span
                    aria-hidden="true"
                    class="block whitespace-nowrap text-[clamp(5rem,11vw,10rem)] leading-[0.85] font-bold tracking-[-0.03em] text-cosmic-900 [writing-mode:vertical-rl] rotate-180"
                >Milky Way</span>
            </div>

            {{-- Plates, first on small screens so the section opens on an image --}}
            <div class="order-first grid grid-cols-3 gap-3 lg:order-none lg:col-start-3 lg:row-start-1 lg:flex lg:flex-col lg:gap-4">
                @foreach ($plates as $plate)
                    <picture>
                        <source type="image/webp" srcset="/images/showcase/{{ $plate['key'] }}.webp" />
                        <img
                            src="/images/showcase/{{ $plate['key'] }}.jpg"
                            alt="{{ $plate['alt'] }}"
                            loading="lazy"
                            class="aspect-square w-full rounded-xl object-cover lg:aspect-[4/3]"
                        />
                    </picture>
                @endforeach
            </div>

            {{-- The reasons --}}
            <div class="flex flex-col gap-9 lg:col-start-2 lg:row-start-1 lg:gap-11">
                @foreach ($reasons as $reason)
                    <div class="flex gap-5 sm:gap-6">
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
                    class="font-sub mt-1 inline-flex items-center justify-center gap-2.5 self-start rounded-full border border-cosmic-900/35 px-9 py-4 text-xs font-semibold tracking-[0.2em] text-cosmic-900 uppercase transition duration-200 hover:border-cosmic-900 hover:bg-cosmic-900 hover:text-cream-50 lg:self-end"
                >
                    Chat with us
                </x-site.whatsapp-link>
            </div>
        </div>
    </div>
</section>
