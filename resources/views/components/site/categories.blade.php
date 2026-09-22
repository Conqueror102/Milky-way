@php
    $categories = [
        [
            'key' => 'skincare',
            'panel' => '/images/categories/skincare-panel.jpg',
            'panelWebp' => '/images/categories/skincare-panel.webp',
            'name' => 'Skincare',
            'icon' => 'tint-solid',
            'description' => 'Cleansers, moisturisers, creams, serums, soaps and scrubs for every routine.',
            'tags' => ['Skincare', 'Cleansers'],
        ],
        [
            'key' => 'skincare-vaseline',
            'image' => '/images/categories/skincare2/vaseline',
            'name' => 'Vaseline Body Oils',
            'icon' => 'tint-solid',
            'description' => 'Vaseline Cocoa Radiant, Blue Seal Aloe Fresh and Healthy Bright Daily Brightening.',
            'tags' => ['Skincare', 'Body Oil'],
        ],
        [
            'key' => 'skincare-cosrx',
            'image' => '/images/categories/skincare2/cosrx',
            'name' => 'COSRX Alpha-Arbutin Serum',
            'icon' => 'tint-solid',
            'description' => 'The Alpha-Arbutin 2% Discoloration Care serum, Tranexamic Acid 3% + Niacinamide 5%, 50ml.',
            'tags' => ['Skincare', 'Serum'],
        ],
        [
            'key' => 'skincare-alpha',
            'image' => '/images/categories/skincare2/alphaskincare',
            'name' => 'Alpha Skin Care Renewal Lotion',
            'icon' => 'tint-solid',
            'description' => 'Alpha Skin Care Renewal Body Lotion, 12% Glycolic AHA, 12oz.',
            'tags' => ['Skincare', 'Body Lotion'],
        ],
        [
            'key' => 'skincare-olay',
            'image' => '/images/categories/skincare2/olay',
            'name' => 'Olay Dark Spot Body Lotion',
            'icon' => 'tint-solid',
            'description' => 'Olay Dark Spot Correcting Body Lotion, AHA & Vitamin C + Niacinamide, 17 fl oz.',
            'tags' => ['Skincare', 'Body Lotion'],
        ],
        [
            'key' => 'skincare-aveeno',
            'image' => '/images/categories/skincare2/aveeno',
            'name' => 'Aveeno Body Oil Mist',
            'icon' => 'tint-solid',
            'description' => 'Aveeno Daily Moisturizing Body Oil Mist with oat and jojoba oil, 200ml.',
            'tags' => ['Skincare', 'Body Oil'],
        ],
        [
            'key' => 'skincare-drteals',
            'image' => '/images/categories/skincare2/drteals',
            'name' => "Dr Teal's Citrus Body Care",
            'icon' => 'tint-solid',
            'description' => "Dr Teal's Body Wash, Body Lotion and Shea Sugar Scrub, citrus and vitamin C.",
            'tags' => ['Skincare', 'Body Care'],
        ],
        [
            'key' => 'beauty',
            'panel' => '/images/categories/beauty-panel.jpg',
            'panelWebp' => '/images/categories/beauty-panel.webp',
            'name' => 'Beauty & Cosmetics',
            'icon' => 'paint-brush-solid',
            'description' => 'Makeup, beauty essentials, accessories and the tools to apply them.',
            'tags' => ['Beauty & Cosmetics', 'Makeup'],
        ],
        // The client's real Health & Beauty stock: one product, one card, same as every
        // other card here — not six photos folded into a single tile.
        [
            'key' => 'health-anua',
            'image' => '/images/categories/health/anua',
            'name' => 'Anua Niacinamide Serum',
            'icon' => 'heartbeat-solid',
            'description' => 'Anua Niacinamide 10% + TXA4 Serum, 30ml.',
            'tags' => ['Health & Beauty', 'Serum'],
        ],
        [
            'key' => 'health-niiracell',
            'image' => '/images/categories/health/niiracell',
            'name' => 'Niiracell Glutathione',
            'icon' => 'heartbeat-solid',
            'description' => 'Niiracell Glutathione 90,000mg dietary supplement, 60 capsules.',
            'tags' => ['Health & Beauty', 'Supplement'],
        ],
        [
            'key' => 'health-beefar',
            'image' => '/images/categories/health/beefar',
            'name' => 'Beefar Probiotic Gummies',
            'icon' => 'heartbeat-solid',
            'description' => "Beefar Women's Probiotic + Slippery Elm gummies, 60 count.",
            'tags' => ['Health & Beauty', 'Supplement'],
        ],
        [
            'key' => 'health-neocell',
            'image' => '/images/categories/health/neocell',
            'name' => 'NeoCell Collagen Peptides',
            'icon' => 'heartbeat-solid',
            'description' => 'NeoCell Grassfed Collagen Peptides + Vitamin C, 360 caplets.',
            'tags' => ['Health & Beauty', 'Supplement'],
        ],
        [
            'key' => 'health-ginseng',
            'image' => '/images/categories/health/ginseng',
            'name' => 'Ginseng Six Treasures Tea',
            'icon' => 'heartbeat-solid',
            'description' => 'Kanglai Ginseng Six Treasures Tea, 250g (10g x 25 packs).',
            'tags' => ['Health & Beauty', 'Tea'],
        ],
        [
            'key' => 'health-glutax',
            'image' => '/images/categories/health/glutax',
            'name' => 'Glutax Glutathione Injection',
            'icon' => 'heartbeat-solid',
            'description' => 'Glutax 2000000GX glutathione injection kit. For professional administration.',
            'tags' => ['Health & Beauty', 'Injectable'],
        ],
        [
            'key' => 'health-menopause',
            'image' => '/images/categories/teas/menopause',
            'name' => 'Menopause Tea',
            'icon' => 'heartbeat-solid',
            'description' => 'Herbal tea to support mood and relieve menopause symptoms, 10 tea bags.',
            'tags' => ['Health & Beauty', 'Tea'],
        ],
        [
            'key' => 'health-flattummy',
            'image' => '/images/categories/teas/flattummy',
            'name' => 'Flat Tummy Tea',
            'icon' => 'heartbeat-solid',
            'description' => 'Wins Town 28 Days Detox Flat Tummy Tea, eases bloating and digestion, 28 teabags.',
            'tags' => ['Health & Beauty', 'Tea'],
        ],
        [
            'key' => 'health-grazerdetox',
            'image' => '/images/categories/teas/grazerdetox',
            'name' => 'Grazer Herbal Detox Tea',
            'icon' => 'heartbeat-solid',
            'description' => 'Grazer Herbal Detox Tea, 30 teabags, 100g.',
            'tags' => ['Health & Beauty', 'Tea'],
        ],
        [
            'key' => 'health-doubleroot',
            'image' => '/images/categories/teas/doubleroot',
            'name' => 'Double Root Coffee',
            'icon' => 'heartbeat-solid',
            'description' => 'Double Root Coffee, 100% Arabica.',
            'tags' => ['Health & Beauty', 'Coffee'],
        ],
        [
            'key' => 'health-ginsenggn',
            'image' => '/images/categories/teas/ginsenggn',
            'name' => 'Ginseng Six Treasures Tea (Green Nature)',
            'icon' => 'heartbeat-solid',
            'description' => 'Green Nature Ginseng Six Premium Health Treasures Tea.',
            'tags' => ['Health & Beauty', 'Tea'],
        ],

        // Sexual Enhancement: real stock, one product per card, same as Health & Beauty above.
        [
            'key' => 'sexual-tea',
            'image' => '/images/categories/sexual/tea',
            'name' => 'Erection Tea',
            'icon' => 'heartbeat-solid',
            'description' => 'Tebillah Sam Erection Tea, boosts sex drive and libido. 30 tea bags.',
            'tags' => ['Sexual Enhancement', 'Tea'],
        ],
        [
            'key' => 'sexual-menpower',
            'image' => '/images/categories/sexual/menpower',
            'name' => 'Men Power Gummies',
            'icon' => 'heartbeat-solid',
            'description' => 'Favret H&B Men Power, horny goat weed, coffee and mushroom, 8000mg, 60 gummies.',
            'tags' => ['Sexual Enhancement', 'Supplement'],
        ],
        [
            'key' => 'sexual-coffee',
            'image' => '/images/categories/sexual/coffee',
            'name' => 'X Power Coffee for Men',
            'icon' => 'heartbeat-solid',
            'description' => 'Wins Town X Power Coffee for men, NAFDAC registered. 16 sachets.',
            'tags' => ['Sexual Enhancement', 'Coffee'],
        ],
        [
            'key' => 'body',
            'panel' => '/images/categories/body-panel.jpg',
            'panelWebp' => '/images/categories/body-panel.webp',
            'name' => 'Body Enhancement',
            'icon' => 'gem-solid',
            'description' => 'Body-enhancement and personal-care products for a complete regimen.',
            'tags' => ['Body Enhancement', 'Body care'],
        ],
        [
            'key' => 'body-bootybloom',
            'image' => '/images/categories/body2/bootybloom',
            'name' => 'Juliet Eve Booty Bloom',
            'icon' => 'gem-solid',
            'description' => 'Booty Bloom curve-enhancer shake, smooth butterscotch, with Pueraria Mirifica.',
            'tags' => ['Body Enhancement', 'Shake'],
        ],
        [
            'key' => 'body-beckon',
            'image' => '/images/categories/body2/beckon',
            'name' => 'Beckon Hip & Butt Oils',
            'icon' => 'gem-solid',
            'description' => "Beckon Hip Up Oil, Maca Oil and Hip Butt Oil range for women.",
            'tags' => ['Body Enhancement', 'Oil'],
        ],
        [
            'key' => 'body-macacapsules',
            'image' => '/images/categories/body2/macacapsules',
            'name' => 'Ultimate Maca Capsules',
            'icon' => 'gem-solid',
            'description' => 'Ultimate Maca Capsules, 7500mg, 120 veggy capsules, made for butt/hips.',
            'tags' => ['Body Enhancement', 'Supplement'],
        ],
        [
            'key' => 'body-duozi',
            'image' => '/images/categories/body2/duozi',
            'name' => 'Duozi Hip & Butt Drink',
            'icon' => 'gem-solid',
            'description' => 'Duozi Quick Effect Hip/Butt Enlargement Drink with Maca Plus, 10 x 30ml bottles.',
            'tags' => ['Body Enhancement', 'Drink'],
        ],
        [
            'key' => 'spa',
            'panel' => '/images/categories/spa-panel.jpg',
            'panelWebp' => '/images/categories/spa-panel.webp',
            'name' => 'Spa & Massage',
            'icon' => 'spa-solid',
            'description' => 'Products and essentials for spas, massage businesses and professionals.',
            'tags' => ['Spa & Massage', 'Massage'],
        ],
        [
            'key' => 'spa-massagegun',
            'image' => '/images/categories/spa2/massagegun',
            'name' => 'Percussion Massage Gun',
            'icon' => 'spa-solid',
            'description' => 'Deep-tissue massage gun with 4 head attachments, plus a free keyholder.',
            'tags' => ['Spa & Massage', 'Massage'],
        ],
        [
            'key' => 'spa-mooyam',
            'image' => '/images/categories/spa2/mooyam',
            'name' => 'Mooyam Massage Oil Set',
            'icon' => 'spa-solid',
            'description' => 'Lavender, Frankincense and Sore Muscle massage oils, 8 fl oz each.',
            'tags' => ['Spa & Massage', 'Massage Oil'],
        ],
        [
            'key' => 'spa-woodenroller',
            'image' => '/images/categories/spa2/woodenroller',
            'name' => 'Wooden Spine Roller',
            'icon' => 'spa-solid',
            'description' => 'Handheld wooden roller for back and muscle massage.',
            'tags' => ['Spa & Massage', 'Massage Tool'],
        ],
        [
            'key' => 'wholesale',
            'panel' => '/images/categories/wholesale-panel.jpg',
            'panelWebp' => '/images/categories/wholesale-panel.webp',
            'name' => 'Wholesale',
            'icon' => 'boxes-solid',
            'description' => 'Bulk purchasing for retailers, resellers, salons, spas and beauty businesses.',
            'tags' => ['Wholesale', 'Bulk orders'],
        ],
    ];

    $slides = [
        ['key' => 'applying-product', 'alt' => 'A woman applying a skincare product at her dressing table'],
        ['key' => 'face-roller', 'alt' => 'A woman using a rose quartz face roller'],
        ['key' => 'podium', 'alt' => 'Cosmetic bottles and tubes arranged on a display podium'],
        ['key' => 'spa-massage', 'alt' => 'A woman receiving an oil massage in a spa'],
    ];

    $first = $categories[0];

    // The dropdown/panel show categories, not every individual product card. One entry
    // per unique first tag, in first-appearance order, with its own blurb for the panel.
    $categoryBlurbs = [
        'Skincare' => 'Cleansers, moisturisers, creams, serums, soaps and scrubs for every routine.',
        'Beauty & Cosmetics' => 'Makeup, beauty essentials, accessories and the tools to apply them.',
        'Health & Beauty' => 'Selected health and personal-care products to sit alongside your beauty shelf.',
        'Sexual Enhancement' => 'Products to support intimacy and libido, for individuals and couples.',
        'Body Enhancement' => 'Body-enhancement and personal-care products for a complete regimen.',
        'Spa & Massage' => 'Products and essentials for spas, massage businesses and professionals.',
        'Wholesale' => 'Bulk purchasing for retailers, resellers, salons, spas and beauty businesses.',
    ];

    $filters = collect([['key' => 'All', 'name' => 'All', 'description' => 'Everything we stock, in one place.']])
        ->concat(
            collect($categories)
                ->pluck('tags.0')
                ->unique()
                ->values()
                ->map(fn ($tag) => [
                    'key' => $tag,
                    'name' => $tag,
                    'description' => $categoryBlurbs[$tag] ?? '',
                ])
        )
        ->all();

    $firstFilter = $filters[0];
@endphp

<section id="shop" class="bg-cosmic-950 py-10 lg:py-14">
    <div
        class="mx-auto max-w-7xl px-6 sm:px-8"
        x-data="{
            active: '{{ $firstFilter['key'] }}',
            items: @js(collect($filters)->keyBy('key')),
            get current() { return this.items[this.active] },

            slide: 0,
            slides: @js($slides),
            timer: null,
            play() {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                this.timer = setInterval(() => this.slide = (this.slide + 1) % this.slides.length, 5000);
            },
            pause() { clearInterval(this.timer); },
        }"
        x-init="play()"
    >
        {{-- Heading and category navigation on the same line --}}
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-xl shrink-0">
                <div data-reveal class="flex items-center gap-3">
                    <span class="h-px w-10 bg-gold-400/50"></span>
                    <span class="font-sub text-[0.68rem] font-medium tracking-[0.32em] text-gold-400 uppercase">
                        Shop by category
                    </span>
                </div>
                <h2 data-reveal="lines" style="--d:120" class="mt-4 text-[clamp(1.85rem,3.2vw,2.85rem)] leading-[1.1] font-bold tracking-[-0.01em] text-cream-50">
                    One store, every
                    <span class="font-script tracking-[-0.03em] text-gold-400">beauty</span> need
                </h2>
            </div>

            {{-- Compact Category Dropdown Selector on the Right --}}
            <div class="relative shrink-0" x-data="{ dropdownOpen: false }">
                <button
                    type="button"
                    x-on:click="dropdownOpen = !dropdownOpen"
                    class="font-sub inline-flex items-center gap-2 rounded-full bg-cream-50/95 px-4 py-2.5 text-xs font-semibold text-cosmic-900 shadow-lg shadow-cosmic-950/30 backdrop-blur transition hover:bg-white"
                >
                    <span class="size-2 rounded-full bg-gold-500"></span>
                    <span class="text-cosmic-900/60 font-normal">Category:</span>
                    <span x-text="current.name" class="font-bold"></span>
                    <svg class="size-3.5 text-cosmic-900/60 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    x-show="dropdownOpen"
                    x-cloak
                    x-on:click.outside="dropdownOpen = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                    class="absolute right-0 z-30 mt-2 w-52 origin-top-right rounded-2xl bg-cream-50/98 p-1.5 shadow-2xl ring-1 ring-cosmic-950/10 backdrop-blur-xl"
                >
                    @foreach ($filters as $filter)
                        <button
                            type="button"
                            x-on:click="active = '{{ $filter['key'] }}'; dropdownOpen = false"
                            :class="active === '{{ $filter['key'] }}'
                                ? 'bg-cosmic-900 text-cream-50 font-semibold'
                                : 'text-cosmic-900/75 hover:bg-cosmic-900/5 hover:text-cosmic-900'"
                            class="font-sub flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-xs transition duration-150"
                        >
                            <span>{{ $filter['name'] }}</span>
                            <span x-show="active === '{{ $filter['key'] }}'" class="text-gold-400 text-xs">✓</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- The panel --}}
        <div class="mt-6 grid gap-3 rounded-[2rem] bg-cream-50 p-3 lg:grid-cols-[minmax(0,1fr)_minmax(0,45%)]">

            {{-- Card grid --}}
            <div class="relative order-2 rounded-[1.5rem] lg:order-1">
                <div class="max-h-[30rem] overflow-y-auto py-1 pr-2 pl-1 sm:max-h-[36rem] lg:max-h-[42rem] lg:pr-2.5 [scrollbar-width:thin] [scrollbar-color:#E5671780_transparent] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gold-500/40 hover:[&::-webkit-scrollbar-thumb]:bg-gold-500/80">
                <div class="grid gap-3.5 sm:grid-cols-2">
                    @foreach ($categories as $category)
                        @php $imagePath = $category['image'] ?? '/images/categories/'.$category['key']; @endphp
                        {{-- PORTRAIT CARD WITH IMAGE BEHIND & GLOSSY FADE GLASS EFFECT --}}
                        <article
                            x-show="active === 'All' || active === '{{ $category['tags'][0] }}'"
                            x-on:click="active = '{{ $category['tags'][0] }}'"
                            :class="active === '{{ $category['tags'][0] }}' ? 'ring-2 ring-cosmic-900' : 'ring-1 ring-cosmic-900/8'"
                            class="flex cursor-pointer flex-col rounded-[1.5rem] rounded-b-[2.25rem] bg-white p-3 shadow-[0_12px_32px_-14px_rgba(20,52,82,0.3)] transition duration-200"
                        >
                            {{-- Image radius sits a step tighter than the card's, as in the reference --}}
                            <div class="relative">
                                <picture>
                                    <source type="image/webp" srcset="{{ $imagePath }}.webp" />
                                    <img
                                        src="{{ $imagePath }}.jpg"
                                        alt="{{ $category['name'] }}"
                                        loading="lazy"
                                        class="aspect-square w-full rounded-[1rem] object-cover"
                                    />
                                </picture>
                                <span class="font-sub absolute top-2.5 left-2.5 rounded-full bg-cosmic-950/55 px-2.5 py-1 text-[0.6rem] font-semibold text-white backdrop-blur-sm">
                                    Retail &middot; Bulk
                                </span>
                            </div>

                            <h3 class="mt-4 px-0.5 text-base leading-tight font-bold text-cosmic-900">{{ $category['name'] }}</h3>

                            <p class="font-sub mt-2 line-clamp-2 px-0.5 text-xs leading-relaxed text-cosmic-900/55">
                                {{ $category['description'] }}
                            </p>

                            <div class="mt-3 flex flex-wrap gap-1.5 px-0.5">
                                @foreach ($category['tags'] as $tag)
                                    <span class="font-sub rounded-full bg-cosmic-900/6 px-3 py-1.5 text-[0.65rem] font-medium text-cosmic-900/70">{{ $tag }}</span>
                                @endforeach
                            </div>

                            <x-site.whatsapp-link
                                :message="'Hello Milky Way Cosmetics Stores, I would like to see what you have available under '.$category['name'].'.'"
                                class="font-sub mt-4 block rounded-full bg-cosmic-900 py-3.5 text-center text-sm font-bold text-white transition duration-200 hover:bg-cosmic-800"
                            >View products</x-site.whatsapp-link>
                        </article>
                    @endforeach
                    </div>
                </div>
            </div>

            {{-- Feature panel, driven by the pills --}}
            <div
                x-on:mouseenter="pause()"
                x-on:mouseleave="play()"
                data-reveal="right" style="--d:300"
                class="relative order-1 isolate flex min-h-[22rem] flex-col overflow-hidden rounded-[1.5rem] p-7 lg:order-2 lg:min-h-0 lg:p-8"
            >
                {{-- Server-rendered base: the first slide, and the no-JS fallback --}}
                <img
                    src="/images/showcase/{{ $slides[0]['key'] }}.jpg"
                    alt="{{ $slides[0]['alt'] }}"
                    class="absolute inset-0 -z-30 size-full object-cover"
                />
                <template x-for="(s, i) in slides" :key="s.key">
                    <picture>
                        <source type="image/webp" :srcset="'/images/showcase/' + s.key + '.webp'" />
                        <img
                            :src="'/images/showcase/' + s.key + '.jpg'"
                            :alt="s.alt"
                            :class="slide === i ? 'opacity-100' : 'opacity-0'"
                            class="absolute inset-0 -z-20 size-full object-cover transition-opacity duration-700 ease-out"
                        />
                    </picture>
                </template>
                <div class="absolute inset-0 -z-10 bg-gradient-to-b from-cosmic-950/80 from-5% via-cosmic-950/25 via-45% to-cosmic-950/85"></div>

                {{-- The bold statement, top of the panel --}}
                <h3
                    class="text-[clamp(2.5rem,5vw,4.5rem)] leading-[0.92] font-bold tracking-[-0.02em] text-cream-50"
                    x-text="current.name"
                >{{ $firstFilter['name'] }}</h3>

                <div class="mt-auto pt-10">
                    <div class="mb-5 flex items-center gap-2">
                        <template x-for="(s, i) in slides" :key="s.key">
                            <button
                                type="button"
                                x-on:click="slide = i; pause(); play()"
                                :class="slide === i ? 'w-6 bg-cream-50' : 'w-2 bg-cream-50/45 hover:bg-cream-50/70'"
                                class="h-2 rounded-full transition-all duration-300"
                            ><span class="sr-only" x-text="'Show image ' + (i + 1)"></span></button>
                        </template>
                    </div>

                    <p class="font-sub max-w-sm text-sm leading-relaxed text-cream-50/85" x-text="current.description">{{ $firstFilter['description'] }}</p>

                    <x-site.whatsapp-link
                        class="font-sub mt-6 inline-flex items-center justify-center gap-2 rounded-full bg-cream-50 px-6 py-3.5 text-sm font-semibold text-cosmic-900 transition duration-200 hover:bg-white"
                    >
                        <x-icon name="whatsapp" class="size-4" />
                        Enquire about stock
                    </x-site.whatsapp-link>
                </div>
            </div>
        </div>
    </div>
</section>
