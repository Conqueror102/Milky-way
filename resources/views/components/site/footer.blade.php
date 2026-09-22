@php
    $categories = [
        ['label' => 'Skincare & Facials', 'href' => '#categories'],
        ['label' => 'Body Enhancement', 'href' => '#categories'],
        ['label' => 'Spa & Wellness', 'href' => '#categories'],
        ['label' => 'Bulk Wholesale', 'href' => '#categories'],
    ];

    $quickLinks = [
        ['label' => 'Home', 'href' => '#top'],
        ['label' => 'Categories', 'href' => '#categories'],
        ['label' => 'Why Choose Us', 'href' => '#why'],
        ['label' => 'Store Location', 'href' => '#location'],
    ];

    $storeInfo = [
        ['label' => 'C003 Bornu Plaza', 'href' => 'https://wa.me/'.config('milkyway.whatsapp.number')],
        ['label' => 'Tradefair, Lagos', 'href' => 'https://wa.me/'.config('milkyway.whatsapp.number')],
        ['label' => 'Open 24/7 Mon–Sun', 'href' => 'https://wa.me/'.config('milkyway.whatsapp.number')],
        ['label' => 'Lagos · Abuja · Accra', 'href' => 'https://wa.me/'.config('milkyway.whatsapp.number')],
    ];

    $socials = [
        ['label' => 'Instagram', 'href' => config('milkyway.socials.instagram.url')],
        ['label' => 'TikTok', 'href' => config('milkyway.socials.tiktok.url')],
        ['label' => 'Facebook', 'href' => config('milkyway.socials.facebook.url')],
        ['label' => 'WhatsApp', 'href' => 'https://wa.me/'.config('milkyway.whatsapp.number')],
    ];
@endphp

<footer class="relative w-full bg-cosmic-950 overflow-hidden pt-16 sm:pt-20 lg:pt-24 border-t border-gold-400/20">
    {{-- Soft Gold Ambient Top Glow --}}
    <div class="pointer-events-none absolute -top-32 left-1/2 -translate-x-1/2 w-[42rem] h-60 bg-gold-400/10 blur-3xl rounded-full"></div>

    {{-- Main Content Container --}}
    <div class="relative z-10 mx-auto max-w-7xl px-6 sm:px-8 lg:px-12">
        
        {{-- Top Navigation & Brand Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 pb-12 sm:pb-16">
            
            {{-- Brand Column --}}
            <div class="lg:col-span-5 flex flex-col justify-between space-y-6">
                <div>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                        <span class="grid size-9 place-items-center rounded-full bg-cosmic-900 text-gold-400 ring-1 ring-gold-400/30 group-hover:ring-gold-400/60 transition duration-200">
                            <x-site.logo-mark class="size-5" />
                        </span>
                        <span class="font-sans text-2xl sm:text-3xl font-bold tracking-tight text-cream-50 uppercase">
                            MILKY WAY
                        </span>
                    </a>

                    <p class="font-sub text-sm text-cream-100/70 leading-relaxed max-w-sm mt-4">
                        Milky Way Cosmetics Stores supplies quality skincare, beauty, body enhancement and spa products at wholesale and retail prices, from Amuwo-Odofin, Lagos.
                    </p>
                </div>

                <div>
                    <x-site.whatsapp-link
                        message="Hello Milky Way Cosmetics Stores, I would like to make an enquiry about your products."
                        class="inline-flex items-center gap-2.5 rounded-full bg-gold-400/10 hover:bg-gold-400/20 border border-gold-400/25 px-4 py-2 text-xs font-medium text-gold-300 hover:text-gold-200 transition duration-200"
                    >
                        <span class="size-2 rounded-full bg-gold-400 animate-pulse"></span>
                        <span class="font-sub">Chat on WhatsApp · 24/7 Available</span>
                    </x-site.whatsapp-link>
                </div>
            </div>

            {{-- 4 Link Columns --}}
            <div class="lg:col-span-7 grid grid-cols-2 sm:grid-cols-4 gap-8 font-sub">
                
                {{-- Col 1: Quick Links --}}
                <div class="flex flex-col space-y-3.5">
                    <h4 class="text-xs font-bold uppercase tracking-[0.18em] text-gold-400">Quick link</h4>
                    <ul class="space-y-2.5 text-[13px] text-cream-100/70">
                        @foreach ($quickLinks as $link)
                            <li>
                                <a href="{{ $link['href'] }}" class="hover:text-gold-300 transition-colors duration-150">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Col 2: Categories --}}
                <div class="flex flex-col space-y-3.5">
                    <h4 class="text-xs font-bold uppercase tracking-[0.18em] text-gold-400">Categories</h4>
                    <ul class="space-y-2.5 text-[13px] text-cream-100/70">
                        @foreach ($categories as $link)
                            <li>
                                <a href="{{ $link['href'] }}" class="hover:text-gold-300 transition-colors duration-150">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Col 3: Store Info --}}
                <div class="flex flex-col space-y-3.5">
                    <h4 class="text-xs font-bold uppercase tracking-[0.18em] text-gold-400">Store Info</h4>
                    <ul class="space-y-2.5 text-[13px] text-cream-100/70">
                        @foreach ($storeInfo as $link)
                            <li>
                                <a href="{{ $link['href'] }}" target="_blank" rel="noopener" class="hover:text-gold-300 transition-colors duration-150">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Col 4: Social --}}
                <div class="flex flex-col space-y-3.5">
                    <h4 class="text-xs font-bold uppercase tracking-[0.18em] text-gold-400">Social</h4>
                    <ul class="space-y-2.5 text-[13px] text-cream-100/70">
                        @foreach ($socials as $link)
                            <li>
                                <a href="{{ $link['href'] }}" target="_blank" rel="noopener" class="hover:text-gold-300 transition-colors duration-150">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>

        {{-- Divider & Copyright / Metadata Row --}}
        <div class="relative z-10 pt-8 pb-4 border-t border-gold-400/15 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-cream-100/50 font-sub">
            <div>
                © {{ date('Y') }} Milky Way Cosmetics. All rights reserved.
            </div>
            <div>
                Tradefair Complex, Lagos · Wholesale & Retail
            </div>
        </div>

    </div>

    {{-- Giant Luminous Brand Watermark Typography (Full Width Edge-to-Edge) --}}
    <div class="relative w-full overflow-hidden pointer-events-none select-none -mb-3 sm:-mb-5 lg:-mb-8">
        {{-- Ambient golden backlight --}}
        <div class="absolute inset-x-0 bottom-0 h-44 bg-[radial-gradient(ellipse_at_bottom,rgba(229,103,23,0.28),transparent_70%)] blur-2xl"></div>

        {{-- Watermark Text: Center letters shine in Soft Gold, fading to Cosmic Blue edges --}}
        <div class="w-full text-center whitespace-nowrap font-sub font-black uppercase tracking-tight text-[18vw] leading-[0.75] bg-[radial-gradient(ellipse_80%_100%_at_50%_30%,#f6d2b6_0%,#E56717_35%,#a73d10_65%,rgba(18,42,66,0.15)_95%)] bg-clip-text text-transparent">
            MILKYWAY
        </div>
    </div>
</footer>
