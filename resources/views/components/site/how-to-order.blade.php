@php
    $steps = [
        ['icon' => 'search-solid', 'title' => $site->text('how.step_1_title'), 'description' => $site->text('how.step_1_text')],
        ['icon' => 'clipboard-check-solid', 'title' => $site->text('how.step_2_title'), 'description' => $site->text('how.step_2_text')],
        ['icon' => 'whatsapp', 'title' => $site->text('how.step_3_title'), 'description' => $site->text('how.step_3_text')],
        ['icon' => 'check-solid', 'title' => $site->text('how.step_4_title'), 'description' => $site->text('how.step_4_text')],
        ['icon' => 'truck-solid', 'title' => $site->text('how.step_5_title'), 'description' => $site->text('how.step_5_text')],
    ];
@endphp

<section id="how" class="relative overflow-hidden bg-canvas py-10 lg:py-14">

    {{-- Fills the space beside the heading: three portraits dropping out of the
         section above, the centre one hanging lower. --}}
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 hidden lg:block">
        <div data-stagger="160" data-stagger-from="200" class="mx-auto flex max-w-7xl items-start justify-end gap-3 px-6 sm:px-8">
            @foreach (['a' => 'h-60', 'b' => 'h-80', 'c' => 'h-60'] as $plate => $height)
                @if ($custom = $site->image('how.photo_'.$loop->iteration))
                    <x-site.photo
                        :src="$custom"
                        alt=""
                        sizes="9rem"
                        :widths="[300]"
                        loading="lazy"
                        data-reveal="down"
                        :class="$height.' w-[7.5rem] rounded-b-[1.5rem] object-cover xl:w-[8.5rem]'"
                    />
                    @continue
                @endif
                <picture>
                    <source type="image/webp" srcset="/images/how/{{ $plate }}.webp" />
                    <img
                        src="/images/how/{{ $plate }}.jpg"
                        alt=""
                        loading="lazy"
                        data-reveal="down" class="{{ $height }} w-[7.5rem] rounded-b-[1.5rem] object-cover xl:w-[8.5rem]"
                    />
                </picture>
            @endforeach
        </div>
    </div>

    <div class="relative mx-auto max-w-5xl px-6 sm:px-8">

        {{-- Heading --}}
        <span data-reveal class="font-sub inline-block rounded-full bg-gold-300 px-4 py-2 text-sm font-semibold text-cosmic-900">
            {{ $site->text('how.eyebrow') }}
        </span>

        <h2 data-reveal="lines" style="--d:120" class="mt-5 max-w-lg text-[clamp(1.85rem,3.2vw,2.85rem)] leading-[1.15] font-bold tracking-[-0.01em] text-cosmic-900">
            {{ $site->text('how.heading') }}
            <span class="font-script text-[1.15em] leading-[0.8] tracking-[-0.03em] text-gold-700">{{ $site->text('how.heading_accent') }}</span>
        </h2>

        <p data-reveal style="--d:300" class="font-sub mt-5 max-w-lg text-base leading-relaxed text-cosmic-900/60 lg:text-lg">
            {{ $site->text('how.intro') }}
        </p>

        {{-- The steps, stepping down the page --}}
        <ol class="mt-12 lg:mt-14">
            @foreach ($steps as $step)
                @php $onLeft = $loop->odd; @endphp

                <li>
                    <article data-reveal="{{ $onLeft ? 'left' : 'right' }}" @class([
                        'relative overflow-hidden rounded-2xl py-7 pr-7 pl-18 ring-1 ring-cosmic-900/5',
                        'bg-gold-100' => $onLeft,
                        'bg-cream-100' => ! $onLeft,
                        'lg:w-[54%]' => true,
                        'lg:ml-auto' => ! $onLeft,
                    ])>
                        {{-- The tab carries the step number, where the reference carries a duration --}}
                        <span @class([
                            'absolute top-3 bottom-3 left-3 flex w-10 items-center justify-center rounded-full',
                            'bg-cosmic-900' => $onLeft,
                            'bg-gold-700' => ! $onLeft,
                        ])>
                            <span class="font-sub rotate-180 text-[0.7rem] font-bold tracking-[0.16em] text-cream-50 uppercase [writing-mode:vertical-rl]">
                                Step {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </span>

                        <div class="flex items-center gap-3">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-white ring-1 ring-cosmic-900/10">
                                <x-icon :name="$step['icon']" class="size-5 text-gold-700" />
                            </span>
                            <h3 class="text-xl leading-tight font-bold text-cosmic-900">{{ $step['title'] }}</h3>
                        </div>

                        <p class="font-sub mt-4 text-base leading-relaxed text-cosmic-900/65">
                            {{ $step['description'] }}
                        </p>
                    </article>

                    {{-- Dashed elbow into the next card. Drawn with borders rather than SVG so
                         the dashes keep their shape at any width. --}}
                    @unless ($loop->last)
                        <div data-reveal="fade" style="--d:350" aria-hidden="true" class="relative h-8 lg:h-14">
                            <span @class([
                                'absolute top-0 h-full border-dashed border-gold-500/70 border-t-2',
                                'left-[46%] w-[12%] rounded-tr-2xl border-r-2' => $onLeft,
                                'left-[42%] w-[12%] rounded-tl-2xl border-l-2' => ! $onLeft,
                            ])></span>
                            <span @class([
                                'absolute -bottom-1 text-gold-600',
                                'left-[58%] -translate-x-1/2' => $onLeft,
                                'left-[42%] -translate-x-1/2' => ! $onLeft,
                            ])>
                                <x-icon name="angle-down-solid" class="size-4" />
                            </span>
                        </div>
                    @endunless
                </li>
            @endforeach
        </ol>

        <div class="mt-12">
            <x-site.whatsapp-link
                data-reveal
                class="font-sub inline-flex items-center justify-center gap-3 rounded-full bg-cosmic-900 px-8 py-4 text-sm font-semibold text-cream-50 transition duration-200 hover:bg-cosmic-950"
            >
                <x-icon name="whatsapp" class="size-5 text-gold-400" />
                {{ $site->text('how.button') }}
            </x-site.whatsapp-link>
        </div>
    </div>
</section>
