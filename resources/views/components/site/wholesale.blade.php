@php
    $askAbout = [
        'Available products',
        'Wholesale prices',
        'Minimum quantities',
        'Current stock',
        'Bulk orders',
        'Delivery options',
    ];

    /** Telling buyers what to send speeds up the first reply and saves a round of questions. */
    $tellUs = [
        'The kind of business you run',
        'Where you are based',
        'Which categories you stock',
        'Roughly how much you buy at a time',
    ];

    $enquiry = 'Hello Milky Way Cosmetics Stores. I run a [salon / shop / resale business] in [area] '
        .'and I would like wholesale prices for [category]. I buy roughly [quantity] at a time.';
@endphp

<section id="wholesale" class="bg-gold-300 py-10 lg:py-14">
    <div class="mx-auto max-w-7xl px-6 sm:px-8">

        <div class="grid gap-10 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:items-start lg:gap-16">

            {{-- The pitch --}}
            <div data-stagger="110">
                <div data-reveal class="flex items-center gap-3">
                    <span class="h-px w-10 bg-cosmic-900/40"></span>
                    <span class="font-sub text-[0.68rem] font-medium tracking-[0.32em] text-cosmic-900/70 uppercase">
                        Wholesale
                    </span>
                </div>

                <h2 data-reveal="lines" style="--d:120" class="mt-4 text-[clamp(1.85rem,3.2vw,2.85rem)] leading-[1.1] font-bold tracking-[-0.01em] text-cosmic-900">
                    Stock your beauty
                    <span class="font-script text-[1.15em] leading-[0.8] tracking-[-0.03em] text-cosmic-900">business</span>
                </h2>

                <p data-reveal style="--d:300" class="font-sub mt-5 text-base leading-relaxed text-cosmic-900/75 lg:text-lg">
                    Retailer, reseller, salon owner, spa operator or beauty entrepreneur &mdash;
                    you can buy here in bulk. Starting out or restocking, message us and we will
                    tell you what we have and what it costs.
                </p>

                <x-site.whatsapp-link
                    data-reveal
                    :message="$enquiry"
                    class="font-sub mt-8 inline-flex items-center justify-center gap-3 rounded-full bg-cosmic-900 px-8 py-4 text-sm font-semibold text-cream-50 transition duration-200 hover:bg-cosmic-950"
                >
                    <x-icon name="whatsapp" class="size-5 text-gold-400" />
                    Get wholesale prices
                </x-site.whatsapp-link>

                <p data-reveal class="font-sub mt-4 max-w-sm text-sm leading-relaxed text-cosmic-900/60">
                    Opens WhatsApp with a message you can fill in. {{ config('milkyway.hours') }}.
                </p>

                {{-- What we have instead of stockist logos: facts --}}
                <dl data-reveal class="mt-9 grid gap-5 border-t border-cosmic-900/15 pt-7 sm:grid-cols-3">
                    <div>
                        <dt class="font-sub text-[0.62rem] font-semibold tracking-[0.2em] text-cosmic-900/55 uppercase">Delivers to</dt>
                        <dd class="font-sub mt-1.5 text-sm font-medium text-cosmic-900">{{ implode(' · ', config('milkyway.delivery_areas')) }}</dd>
                    </div>
                    <div>
                        <dt class="font-sub text-[0.62rem] font-semibold tracking-[0.2em] text-cosmic-900/55 uppercase">Walk in</dt>
                        <dd class="font-sub mt-1.5 text-sm font-medium text-cosmic-900">{{ config('milkyway.address.line') }}</dd>
                    </div>
                    <div>
                        <dt class="font-sub text-[0.62rem] font-semibold tracking-[0.2em] text-cosmic-900/55 uppercase">Or call</dt>
                        <dd class="font-sub mt-1.5 text-sm font-medium text-cosmic-900">
                            <a href="tel:{{ config('milkyway.phone_dial') }}" class="underline-offset-4 hover:underline">{{ config('milkyway.phone') }}</a>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Both sides of the conversation --}}
            <div data-stagger="150" data-stagger-from="250" class="grid gap-4 sm:grid-cols-2">
                <div data-reveal="right" class="rounded-[1.5rem] bg-cream-50 p-6 ring-1 ring-cosmic-900/10 shadow-[0_10px_28px_-16px_rgba(20,52,82,0.35)] lg:p-7">
                    <div class="flex items-center gap-2.5">
                        <x-icon name="check-solid" class="size-4 text-gold-700" />
                        <h3 class="font-sub text-sm font-bold tracking-[0.14em] text-cosmic-900 uppercase">Ask us about</h3>
                    </div>
                    <ul class="mt-5 grid gap-3">
                        @foreach ($askAbout as $item)
                            <li class="font-sub flex gap-2.5 text-base text-cosmic-900/75">
                                <span class="mt-2 size-1.5 shrink-0 rounded-full bg-gold-500"></span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div data-reveal="right" class="rounded-[1.5rem] bg-cosmic-900 p-6 lg:p-7">
                    <div class="flex items-center gap-2.5">
                        <x-icon name="pen-solid" class="size-4 text-gold-400" />
                        <h3 class="font-sub text-sm font-bold tracking-[0.14em] text-cream-50 uppercase">Tell us</h3>
                    </div>
                    <ul class="mt-5 grid gap-3">
                        @foreach ($tellUs as $item)
                            <li class="font-sub flex gap-2.5 text-base text-cream-50/75">
                                <span class="mt-2 size-1.5 shrink-0 rounded-full bg-gold-400"></span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                    <p class="font-sub mt-6 text-sm leading-relaxed text-cream-50/55">
                        Four lines in your first message saves a day of back and forth.
                    </p>
                </div>

                {{-- Fills the foot of the two cards and shows the kind of stock on offer --}}
                <div data-reveal="clip" class="overflow-hidden rounded-[1.5rem] sm:col-span-2">
                    <picture>
                        <source type="image/webp" srcset="/images/wholesale-strip-800.webp 800w, /images/wholesale-strip-1200.webp 1200w" sizes="(min-width: 1024px) 45vw, 100vw" />
                        <img
                            src="/images/wholesale-strip-1200.jpg"
                            srcset="/images/wholesale-strip-800.jpg 800w, /images/wholesale-strip-1200.jpg 1200w"
                            sizes="(min-width: 1024px) 45vw, 100vw"
                            alt="Aromatic spa massage oil and botanical bath scrub on a marble surface, lit by candles"
                            loading="lazy"
                            class="aspect-[4/1] w-full object-cover"
                        />
                    </picture>
                </div>
            </div>
        </div>
    </div>
</section>
