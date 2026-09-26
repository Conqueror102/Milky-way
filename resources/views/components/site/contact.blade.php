@php
    $mapQuery = rawurlencode(config('milkyway.map_query'));
    $directions = 'https://www.google.com/maps/search/?api=1&query='.$mapQuery;
    $embed = 'https://www.google.com/maps?q='.$mapQuery.'&output=embed';
@endphp

<section id="contact" class="relative isolate overflow-hidden bg-sand py-16 lg:py-24">
    <div class="relative z-10 mx-auto grid max-w-7xl gap-12 px-6 sm:px-8 lg:grid-cols-2 lg:items-center lg:gap-16">

        {{-- Where, when, how --}}
        <div>
            <div data-reveal class="flex items-center gap-3">
                <span class="h-px w-10 bg-cosmic-900/40"></span>
                <span class="font-sub text-[0.68rem] font-bold tracking-[0.32em] text-cosmic-900 uppercase">
                    {{ $site->text('contact.eyebrow') }}
                </span>
            </div>

            <h2 data-reveal="lines" style="--d:120" class="mt-4 text-[clamp(2.1rem,3.6vw,3.2rem)] leading-[1.1] font-bold tracking-[-0.01em] text-cosmic-950">
                {{ $site->text('contact.heading') }}
                <span class="font-script text-[1.25em] leading-[0.8] tracking-[-0.03em] text-cream-50">{{ $site->text('contact.heading_accent') }}</span>
            </h2>

            <p data-reveal style="--d:180" class="font-sub mt-3 max-w-md text-sm sm:text-base text-cosmic-900/90 leading-relaxed">
                {{ $site->text('contact.intro') }}
            </p>

            <dl data-stagger="120" data-stagger-from="250" class="mt-8 grid gap-4">
                {{-- Shop --}}
                <div data-reveal class="flex items-start gap-4 rounded-2xl bg-white/85 p-4.5 backdrop-blur-md ring-1 ring-gold-500/15 shadow-sm shadow-gold-500/5 transition duration-200 hover:bg-white hover:ring-gold-500/35 hover:shadow-md">
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-gold-500 text-white shadow-md shadow-gold-500/25">
                        <x-icon name="map-marker-alt-solid" class="size-5 text-white" />
                    </span>
                    <div>
                        <dt class="font-sub text-[0.62rem] font-bold tracking-[0.2em] text-gold-500 uppercase">{{ $site->text('contact.shop_label') }}</dt>
                        <dd class="font-sub mt-1 text-sm font-semibold leading-relaxed text-cosmic-950 sm:text-base">
                            {{ config('milkyway.address.line') }},<br>{{ config('milkyway.address.area') }}
                        </dd>
                    </div>
                </div>

                {{-- Hours --}}
                <div data-reveal class="flex items-start gap-4 rounded-2xl bg-white/85 p-4.5 backdrop-blur-md ring-1 ring-gold-500/15 shadow-sm shadow-gold-500/5 transition duration-200 hover:bg-white hover:ring-gold-500/35 hover:shadow-md">
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-gold-500 text-white shadow-md shadow-gold-500/25">
                        <x-icon name="clock-solid" class="size-5 text-white" />
                    </span>
                    <div>
                        <dt class="font-sub text-[0.62rem] font-bold tracking-[0.2em] text-gold-500 uppercase">{{ $site->text('contact.hours_label') }}</dt>
                        <dd class="font-sub mt-1 text-sm font-semibold text-cosmic-950 sm:text-base">{{ config('milkyway.hours') }}</dd>
                    </div>
                </div>

                {{-- Phone --}}
                <div data-reveal class="flex items-start gap-4 rounded-2xl bg-white/85 p-4.5 backdrop-blur-md ring-1 ring-gold-500/15 shadow-sm shadow-gold-500/5 transition duration-200 hover:bg-white hover:ring-gold-500/35 hover:shadow-md">
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-gold-500 text-white shadow-md shadow-gold-500/25">
                        <x-icon name="phone-solid" class="size-5 text-white" />
                    </span>
                    <div>
                        <dt class="font-sub text-[0.62rem] font-bold tracking-[0.2em] text-gold-500 uppercase">{{ $site->text('contact.phone_label') }}</dt>
                        <dd class="font-sub mt-1 text-sm font-semibold text-cosmic-950 sm:text-base">
                            <a href="tel:{{ config('milkyway.phone_dial') }}" class="underline-offset-4 hover:underline hover:text-gold-500 transition-colors">
                                {{ config('milkyway.phone') }}
                            </a>
                        </dd>
                    </div>
                </div>
            </dl>

            {{-- CTA buttons --}}
            <div data-reveal style="--d:650" class="mt-9 flex flex-wrap gap-3">
                <a
                    href="tel:{{ config('milkyway.phone_dial') }}"
                    class="font-sub inline-flex items-center justify-center gap-2.5 rounded-full bg-white px-7 py-3.5 text-sm font-semibold text-gold-500 shadow-lg shadow-cosmic-950/10 transition duration-200 hover:bg-cream-50 hover:scale-[1.02]"
                >
                    <x-icon name="phone-solid" class="size-4 text-gold-500" />
                    {{ $site->text('contact.call_button') }}
                </a>

                <x-site.whatsapp-link
                    class="font-sub inline-flex items-center justify-center gap-2.5 rounded-full bg-cosmic-900 px-7 py-3.5 text-sm font-semibold text-cream-50 shadow-md shadow-cosmic-950/15 transition duration-200 hover:bg-cosmic-950 hover:scale-[1.02]"
                >
                    <x-icon name="whatsapp" class="size-4 text-[#25D366]" />
                    {{ $site->text('contact.whatsapp_button') }}
                </x-site.whatsapp-link>

                <a
                    href="{{ $directions }}"
                    target="_blank"
                    rel="noopener"
                    class="font-sub inline-flex items-center justify-center gap-2.5 rounded-full border border-gold-500/30 bg-white/90 px-7 py-3.5 text-sm font-semibold text-cosmic-950 shadow-sm backdrop-blur-md transition duration-200 hover:bg-white hover:border-gold-500 hover:scale-[1.02]"
                >
                    <x-icon name="map-marker-alt-solid" class="size-4 text-gold-500" />
                    {{ $site->text('contact.directions_button') }}
                </a>
            </div>
        </div>

        {{-- Map with rounded glass frame --}}
        <div data-reveal="right" style="--d:250" class="overflow-hidden rounded-[2rem] bg-white/90 p-2.5 shadow-xl shadow-cosmic-950/8 backdrop-blur-md ring-1 ring-gold-500/20">
            <iframe
                src="{{ $embed }}"
                title="Map showing Milkyway Cosmetics Stores at {{ config('milkyway.address.line') }}"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                class="block h-[22rem] w-full rounded-[1.5rem] border-0 lg:h-[28rem]"
            ></iframe>
        </div>
    </div>
</section>
