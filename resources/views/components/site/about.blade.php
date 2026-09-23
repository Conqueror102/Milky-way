@php
    $distributes = ['Health', 'Beauty', 'Skincare', 'Body Enhancement', 'Spa'];
@endphp

<section id="about" class="bg-canvas py-10 lg:py-14">
    <div class="mx-auto max-w-7xl px-6 sm:px-8">

        {{-- Heading --}}
        <div class="max-w-2xl">
            <div data-reveal class="flex items-center gap-3">
                <span class="h-px w-10 bg-gold-500/60"></span>
                <span class="font-sub text-[0.68rem] font-medium tracking-[0.32em] text-gold-700 uppercase">
                    About us
                </span>
            </div>

            <h2 data-reveal="lines" style="--d:120" class="mt-4 text-[clamp(1.85rem,3.2vw,2.85rem)] leading-[1.1] font-bold tracking-[-0.01em] text-cosmic-900">
                Based in Lagos. Stocked for
                <span class="font-script text-[1.15em] leading-[0.8] tracking-[-0.03em] text-gold-700">everyone</span>
            </h2>
        </div>

        <div data-stagger="130" data-stagger-from="200" class="mt-10 grid gap-4 lg:mt-12 lg:grid-cols-12">

            {{-- The statement --}}
            <div data-reveal class="flex flex-col justify-between rounded-[1.5rem] bg-cosmic-900 p-7 lg:col-span-7 lg:p-9">
                <p class="font-sub text-base leading-relaxed text-cream-50/80 lg:text-lg">
                    Milkyway Cosmetics Stores is a beauty, cosmetics and personal-care business in
                    Amuwo-Odofin, Lagos. We distribute health, beauty, skincare, body-enhancement and
                    spa products &mdash; wholesale and retail &mdash; to individuals and to the
                    businesses that stock them.
                </p>

                <figure class="mt-8 border-t border-cream-50/15 pt-7">
                    <figcaption class="font-sub text-[0.62rem] font-semibold tracking-[0.2em] text-gold-400 uppercase">
                        Our goal is simple
                    </figcaption>
                    <blockquote class="mt-3 text-[clamp(1.15rem,1.8vw,1.6rem)] leading-[1.35] font-bold text-cream-50">
                        To make quality beauty and personal-care products accessible, with service
                        that suits both individual customers and beauty businesses.
                    </blockquote>
                </figure>
            </div>

            {{-- Image --}}
            <div data-reveal="scale" class="relative min-h-[18rem] overflow-hidden rounded-[1.5rem] lg:col-span-5">
                <picture>
                    <source type="image/webp" srcset="/images/categories/skincare2/drteals.webp" sizes="(min-width: 1024px) 40vw, 100vw" />
                    <img
                        src="/images/categories/skincare2/drteals.jpg"
                        alt="Dr Teal's body care range, part of Milkyway's real stock"
                        loading="lazy"
                        class="absolute inset-0 size-full object-cover"
                    />
                </picture>
            </div>

            {{-- Where --}}
            <div data-reveal class="rounded-[1.5rem] bg-white p-7 ring-1 ring-cosmic-900/8 lg:col-span-4">
                <x-icon name="map-marker-alt-solid" class="size-6 text-gold-700" />
                <h3 class="font-sub mt-4 text-sm font-bold tracking-[0.18em] text-cosmic-900 uppercase">Where to find us</h3>
                <p class="font-sub mt-3 text-base leading-relaxed text-cosmic-900/65">
                    {{ config('milkyway.address.line') }},<br>{{ config('milkyway.address.area') }}
                </p>
                <p class="font-sub mt-3 text-sm text-cosmic-900/50">{{ config('milkyway.hours') }}</p>
            </div>

            {{-- What --}}
            <div data-reveal class="rounded-[1.5rem] bg-white p-7 ring-1 ring-cosmic-900/8 lg:col-span-4">
                <x-icon name="layer-group-solid" class="size-6 text-gold-700" />
                <h3 class="font-sub mt-4 text-sm font-bold tracking-[0.18em] text-cosmic-900 uppercase">What we distribute</h3>
                <ul class="mt-4 flex flex-wrap gap-2">
                    @foreach ($distributes as $item)
                        <li class="font-sub rounded-full bg-cosmic-900/6 px-3.5 py-1.5 text-sm font-medium text-cosmic-900/75">{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- How far --}}
            <div data-reveal class="rounded-[1.5rem] bg-white p-7 ring-1 ring-cosmic-900/8 lg:col-span-4">
                <x-icon name="truck-solid" class="size-6 text-gold-700" />
                <h3 class="font-sub mt-4 text-sm font-bold tracking-[0.18em] text-cosmic-900 uppercase">How far we reach</h3>
                <p class="font-sub mt-3 text-base leading-relaxed text-cosmic-900/65">
                    Delivery across Lagos, Ogun and Abuja.
                </p>
                <p class="font-sub mt-3 text-sm leading-relaxed text-cosmic-900/50">
                    Our customers go further still &mdash; as far as Accra, Ghana.
                </p>
            </div>
        </div>
    </div>
</section>
