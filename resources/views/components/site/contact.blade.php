@php
    $mapQuery = rawurlencode(config('milkyway.map_query'));
    $directions = 'https://www.google.com/maps/search/?api=1&query='.$mapQuery;
    $embed = 'https://www.google.com/maps?q='.$mapQuery.'&output=embed';
@endphp

<section id="contact" class="relative isolate bg-cream-950 py-10 lg:py-14">
    {{-- Paper texture under a warm dim, measured so cream text holds above 9:1 --}}
    <picture>
        <source type="image/webp" srcset="/images/paper-1000.webp 1000w, /images/paper-1600.webp 1600w" sizes="100vw" />
        <img
            src="/images/paper-1600.jpg"
            srcset="/images/paper-1000.jpg 1000w, /images/paper-1600.jpg 1600w"
            sizes="100vw"
            alt=""
            loading="lazy"
            class="absolute inset-0 -z-20 size-full object-cover"
        />
    </picture>
    <div class="absolute inset-0 -z-10 bg-black/55"></div>

    <div class="mx-auto grid max-w-7xl gap-10 px-6 sm:px-8 lg:grid-cols-2 lg:items-center lg:gap-16">

        {{-- Where, when, how --}}
        <div>
            <div class="flex items-center gap-3">
                <span class="h-px w-10 bg-gold-300/50"></span>
                <span class="font-sub text-[0.68rem] font-medium tracking-[0.32em] text-gold-300 uppercase">
                    Contact
                </span>
            </div>

            <h2 class="mt-4 text-[clamp(1.85rem,3.2vw,2.85rem)] leading-[1.1] font-bold tracking-[-0.01em] text-cream-50">
                Come and see
                <span class="font-script text-[1.15em] leading-[0.8] tracking-[-0.03em] text-gold-300">us</span>
            </h2>

            <dl class="mt-8 grid gap-6">
                <div class="flex gap-4">
                    <x-icon name="map-marker-alt-solid" class="mt-0.5 size-5 shrink-0 text-gold-300" />
                    <div>
                        <dt class="font-sub text-[0.62rem] font-semibold tracking-[0.2em] text-cream-50/45 uppercase">The shop</dt>
                        <dd class="font-sub mt-1.5 text-base leading-relaxed text-cream-50 lg:text-lg">
                            {{ config('milkyway.address.line') }},<br>{{ config('milkyway.address.area') }}
                        </dd>
                    </div>
                </div>

                <div class="flex gap-4">
                    <x-icon name="clock-solid" class="mt-0.5 size-5 shrink-0 text-gold-300" />
                    <div>
                        <dt class="font-sub text-[0.62rem] font-semibold tracking-[0.2em] text-cream-50/45 uppercase">Opening hours</dt>
                        <dd class="font-sub mt-1.5 text-base text-cream-50 lg:text-lg">{{ config('milkyway.hours') }}</dd>
                    </div>
                </div>

                <div class="flex gap-4">
                    <x-icon name="phone-solid" class="mt-0.5 size-5 shrink-0 text-gold-300" />
                    <div>
                        <dt class="font-sub text-[0.62rem] font-semibold tracking-[0.2em] text-cream-50/45 uppercase">Phone</dt>
                        <dd class="font-sub mt-1.5 text-base text-cream-50 lg:text-lg">
                            <a href="tel:{{ config('milkyway.phone_dial') }}" class="underline-offset-4 hover:underline">
                                {{ config('milkyway.phone') }}
                            </a>
                        </dd>
                    </div>
                </div>
            </dl>

            <div class="mt-9 flex flex-wrap gap-3">
                <a
                    href="tel:{{ config('milkyway.phone_dial') }}"
                    class="font-sub inline-flex items-center justify-center gap-2.5 rounded-full bg-cream-50 px-7 py-3.5 text-sm font-semibold text-cosmic-900 transition duration-200 hover:bg-white"
                >Call us</a>

                <x-site.whatsapp-link
                    class="font-sub inline-flex items-center justify-center gap-2.5 rounded-full border border-cream-50/25 px-7 py-3.5 text-sm font-semibold text-cream-50 transition duration-200 hover:border-cream-50/60 hover:bg-cream-50/5"
                >
                    <x-icon name="whatsapp" class="size-4 text-gold-300" />
                    WhatsApp us
                </x-site.whatsapp-link>

                <a
                    href="{{ $directions }}"
                    target="_blank"
                    rel="noopener"
                    class="font-sub inline-flex items-center justify-center gap-2.5 rounded-full border border-cream-50/25 px-7 py-3.5 text-sm font-semibold text-cream-50 transition duration-200 hover:border-cream-50/60 hover:bg-cream-50/5"
                >
                    <x-icon name="map-marker-alt-solid" class="size-4 text-gold-300" />
                    Get directions
                </a>
            </div>
        </div>

        {{-- Map, loaded lazily so it never blocks the page --}}
        <div class="overflow-hidden rounded-[1.5rem] bg-black/25 ring-1 ring-cream-50/15">
            <iframe
                src="{{ $embed }}"
                title="Map showing Milky Way Cosmetics Stores at {{ config('milkyway.address.line') }}"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                class="block h-[22rem] w-full border-0 lg:h-[28rem]"
            ></iframe>
        </div>
    </div>
</section>
