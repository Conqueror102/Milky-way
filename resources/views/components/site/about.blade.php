@php
    $distributes = $site->lines('about.what_items');

    $photo = $site->image('about.photo');
@endphp

<section id="about" class="bg-canvas py-10 lg:py-14">
    <div class="mx-auto max-w-7xl px-6 sm:px-8">

        {{-- Heading --}}
        <div class="max-w-2xl">
            <div data-reveal class="flex items-center gap-3">
                <span class="h-px w-10 bg-gold-500/60"></span>
                <span class="font-sub text-[0.68rem] font-medium tracking-[0.32em] text-gold-700 uppercase">
                    {{ $site->text('about.eyebrow') }}
                </span>
            </div>

            <h2 data-reveal="lines" style="--d:120" class="mt-4 text-[clamp(1.85rem,3.2vw,2.85rem)] leading-[1.1] font-bold tracking-[-0.01em] text-cosmic-900">
                {{ $site->text('about.heading') }}
                <span class="font-script text-[1.15em] leading-[0.8] tracking-[-0.03em] text-gold-700">{{ $site->text('about.heading_accent') }}</span>
            </h2>
        </div>

        <div data-stagger="130" data-stagger-from="200" class="mt-10 grid gap-4 lg:mt-12 lg:grid-cols-12">

            {{-- The statement --}}
            <div data-reveal class="flex flex-col justify-between rounded-[1.5rem] bg-cosmic-900 p-7 lg:col-span-7 lg:p-9">
                <p class="font-sub text-base leading-relaxed text-cream-50/80 lg:text-lg">
                    {{ $site->text('about.statement') }}
                </p>

                <figure class="mt-8 border-t border-cream-50/15 pt-7">
                    <figcaption class="font-sub text-[0.62rem] font-semibold tracking-[0.2em] text-gold-400 uppercase">
                        {{ $site->text('about.goal_label') }}
                    </figcaption>
                    <blockquote class="mt-3 text-[clamp(1.15rem,1.8vw,1.6rem)] leading-[1.35] font-bold text-cream-50">
                        {{ $site->text('about.goal') }}
                    </blockquote>
                </figure>
            </div>

            {{-- Image --}}
            <div data-reveal="scale" class="relative min-h-[18rem] overflow-hidden rounded-[1.5rem] lg:col-span-5">
                @if ($photo)
                    <x-site.photo
                        :src="$photo"
                        :alt="$site->alt('about.photo')"
                        sizes="(min-width: 1024px) 40vw, 100vw"
                        :widths="[640, 1000, 1400]"
                        loading="lazy"
                        class="absolute inset-0 size-full object-cover"
                    />
                @else
                <picture>
                    <source type="image/webp" srcset="/images/categories/skincare2/drteals.webp" sizes="(min-width: 1024px) 40vw, 100vw" />
                    <img
                        src="/images/categories/skincare2/drteals.jpg"
                        alt="Dr Teal's body care range, part of Milkyway's real stock"
                        loading="lazy"
                        class="absolute inset-0 size-full object-cover"
                    />
                </picture>
                @endif
            </div>

            {{-- Where --}}
            <div data-reveal class="rounded-[1.5rem] bg-white p-7 ring-1 ring-cosmic-900/8 lg:col-span-4">
                <x-icon name="map-marker-alt-solid" class="size-6 text-gold-700" />
                <h3 class="font-sub mt-4 text-sm font-bold tracking-[0.18em] text-cosmic-900 uppercase">{{ $site->text('about.where_title') }}</h3>
                <p class="font-sub mt-3 text-base leading-relaxed text-cosmic-900/65">
                    {{ config('milkyway.address.line') }},<br>{{ config('milkyway.address.area') }}
                </p>
                <p class="font-sub mt-3 text-sm text-cosmic-900/50">{{ config('milkyway.hours') }}</p>
            </div>

            {{-- What --}}
            <div data-reveal class="rounded-[1.5rem] bg-white p-7 ring-1 ring-cosmic-900/8 lg:col-span-4">
                <x-icon name="layer-group-solid" class="size-6 text-gold-700" />
                <h3 class="font-sub mt-4 text-sm font-bold tracking-[0.18em] text-cosmic-900 uppercase">{{ $site->text('about.what_title') }}</h3>
                <ul class="mt-4 flex flex-wrap gap-2">
                    @foreach ($distributes as $item)
                        <li class="font-sub rounded-full bg-cosmic-900/6 px-3.5 py-1.5 text-sm font-medium text-cosmic-900/75">{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- How far --}}
            <div data-reveal class="rounded-[1.5rem] bg-white p-7 ring-1 ring-cosmic-900/8 lg:col-span-4">
                <x-icon name="truck-solid" class="size-6 text-gold-700" />
                <h3 class="font-sub mt-4 text-sm font-bold tracking-[0.18em] text-cosmic-900 uppercase">{{ $site->text('about.reach_title') }}</h3>
                <p class="font-sub mt-3 text-base leading-relaxed text-cosmic-900/65">
                    {{ $site->text('about.reach_text') }}
                </p>
                @if (filled($site->text('about.reach_note')))
                    <p class="font-sub mt-3 text-sm leading-relaxed text-cosmic-900/50">
                        {{ $site->text('about.reach_note') }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</section>
