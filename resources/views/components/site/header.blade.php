@php
    $links = [
        ['label' => 'Home', 'href' => '#top'],
        ['label' => 'Shop', 'href' => '#shop'],
        ['label' => 'Wholesale', 'href' => '#wholesale'],
        ['label' => 'About', 'href' => '#about'],
        ['label' => 'Contact', 'href' => '#contact'],
    ];
@endphp

{{-- No containing bar: a single glass pill holds the nav, and the wordmark floats bare
     beside it. The three-column grid keeps the wordmark optically centred on the page
     regardless of how wide the nav runs. --}}
<header x-data="{ open: false }" class="fixed inset-x-0 top-0 z-50 px-4 pt-4 sm:px-6 sm:pt-6">
    <div class="mx-auto grid max-w-7xl grid-cols-[1fr_auto_1fr] items-center gap-4">

        {{-- One glass pill holding the whole nav --}}
        <nav class="liquid-glass hidden w-fit items-center gap-1 justify-self-start rounded-full p-1.5 lg:flex">
            @foreach ($links as $link)
                <a
                    href="{{ $link['href'] }}"
                    @class([
                        'font-sub rounded-full px-4 py-2 text-sm font-medium transition duration-200',
                        'bg-cosmic-900 text-cream-50' => $loop->first,
                        'text-cosmic-900/75 hover:bg-white/60 hover:text-cosmic-900' => ! $loop->first,
                    ])
                >{{ $link['label'] }}</a>
            @endforeach
        </nav>

        {{-- Wordmark, in its own glass pill at every size to match the nav and menu button --}}
        <a
            href="{{ route('home') }}"
            aria-label="{{ config('app.name') }} home"
            class="liquid-glass col-start-2 flex items-center gap-2 justify-self-center rounded-full py-1 pr-4 pl-2.5 lg:gap-2.5 lg:py-1.5 lg:pr-5 lg:pl-3"
        >
            <picture>
                <source type="image/webp" srcset="/images/logo.webp" />
                <img src="/images/logo.png" alt="" width="242" height="171" class="h-8 w-auto lg:h-10" />
            </picture>
            <span class="flex flex-col leading-none">
                <span class="text-[0.82rem] font-bold tracking-[0.16em] text-cosmic-900 uppercase lg:text-[0.9rem]">Milky Way</span>
                <span class="font-sub mt-1 text-[0.52rem] tracking-[0.3em] text-cosmic-900/55 uppercase lg:text-[0.58rem]">Cosmetics</span>
            </span>
        </a>

        {{-- Mobile toggle --}}
        <button
            type="button"
            x-on:click="open = ! open"
            :aria-expanded="open ? 'true' : 'false'"
            aria-controls="site-mobile-nav"
            class="liquid-glass col-start-3 grid size-10 shrink-0 place-items-center justify-self-end rounded-full text-cosmic-900 transition hover:bg-white/85 lg:hidden"
        >
            <span class="sr-only">Toggle navigation</span>
            <x-icon name="bars-solid" class="size-5" x-show="! open" />
            <x-icon name="times-solid" class="size-5" x-show="open" x-cloak />
        </button>

        {{-- Mobile panel --}}
        <div
            id="site-mobile-nav"
            x-show="open"
            x-cloak
            x-transition.origin.top.duration.200ms
            x-on:click.outside="open = false"
            class="liquid-glass col-span-3 mt-2 overflow-hidden rounded-3xl p-2 lg:hidden"
        >
            <nav class="grid gap-1">
                @foreach ($links as $link)
                    <a
                        href="{{ $link['href'] }}"
                        x-on:click="open = false"
                        class="font-sub rounded-2xl px-4 py-3 text-sm font-medium text-cosmic-900/80 transition hover:bg-white/70 hover:text-cosmic-900"
                    >{{ $link['label'] }}</a>
                @endforeach
            </nav>
        </div>
    </div>
</header>
