<section class="w-full space-y-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Site content') }}</flux:heading>
        <flux:subheading>{{ __('Change the photos and words on the home page. Pick a section, in the order it appears on the site.') }}</flux:subheading>
    </div>

    <ol class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($sections as $section)
            <li wire:key="section-{{ $section['key'] }}">
                <a
                    href="{{ route('admin.site.edit', $section['key']) }}"
                    wire:navigate
                    class="group flex h-full flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white transition hover:border-zinc-400 hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-500"
                >
                    <div class="relative aspect-[16/9] overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        <img
                            src="{{ $section['preview'] }}"
                            alt="{{ __('Screenshot of the :section section', ['section' => $section['label']]) }}"
                            loading="lazy"
                            class="size-full object-cover object-top transition duration-300 group-hover:scale-[1.02]"
                        >
                        <span class="absolute top-2 left-2 rounded-full bg-zinc-900/75 px-2 py-0.5 text-xs font-medium text-white tabular-nums">
                            {{ $loop->iteration }}
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col gap-1 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <flux:heading size="lg">{{ __($section['label']) }}</flux:heading>
                            @if ($section['changed'] > 0)
                                <flux:badge color="amber" size="sm">{{ trans_choice(':count change|:count changes', $section['changed']) }}</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">{{ __('Original') }}</flux:badge>
                            @endif
                        </div>
                        <flux:text>{{ __($section['description']) }}</flux:text>
                        <span class="mt-auto pt-3 text-sm font-medium text-zinc-900 group-hover:underline dark:text-white">
                            {{ __('Edit section') }} &rarr;
                        </span>
                    </div>
                </a>
            </li>
        @endforeach
    </ol>
</section>
