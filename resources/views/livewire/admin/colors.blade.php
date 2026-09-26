<section class="w-full">
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Colours') }}</flux:heading>
        <flux:subheading>{{ __('Pick the colours the whole site uses. Try a ready-made palette or choose your own, and watch the preview change. Nothing goes live until you save.') }}</flux:subheading>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" class="mb-6" />
    @endif

    <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,26rem)] xl:grid-cols-[minmax(0,1fr)_minmax(0,34rem)]">

        {{-- The preview comes first on phones, so the admin sees the site before the pickers --}}
        <aside class="lg:order-2">
            <div
                class="space-y-3 lg:sticky lg:top-6"
                x-data="{
                    device: 'desktop',
                    scale: 0.3,
                    get width() { return this.device === 'desktop' ? 1280 : 390 },
                    get height() { return this.device === 'desktop' ? 900 : 800 },
                    fit() { this.scale = Math.min(1, this.$refs.frame.clientWidth / this.width) },
                    {{-- Paint the picked colours into the page inside the frame, without saving --}}
                    paint() {
                        const doc = this.$refs.iframe?.contentDocument;
                        if (! doc?.head) return;
                        let style = doc.getElementById('palette-preview');
                        if (! style) {
                            style = doc.createElement('style');
                            style.id = 'palette-preview';
                        }
                        {{-- Last in <head>, so it wins over the saved colours --}}
                        doc.head.appendChild(style);
                        style.textContent = $wire.previewCss;
                    },
                }"
                x-init="fit(); $watch('device', () => $nextTick(() => fit())); $wire.$watch('previewCss', () => paint())"
                x-on:resize.window.debounce.100ms="fit()"
            >
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <flux:heading size="sm">{{ __('Live preview') }}</flux:heading>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-1 rounded-lg bg-zinc-100 p-0.5 text-xs font-medium dark:bg-zinc-800">
                            @foreach ($pages as $key => $info)
                                <button type="button" wire:click="$set('page', '{{ $key }}')" @class(['rounded-md px-2.5 py-1', 'bg-white shadow-sm dark:bg-zinc-600' => $page === $key, 'text-zinc-500' => $page !== $key])>{{ $info['label'] }}</button>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-1 rounded-lg bg-zinc-100 p-0.5 text-xs font-medium dark:bg-zinc-800">
                            <button type="button" x-on:click="device = 'desktop'" :class="device === 'desktop' ? 'bg-white shadow-sm dark:bg-zinc-600' : 'text-zinc-500'" class="rounded-md px-2.5 py-1">{{ __('Computer') }}</button>
                            <button type="button" x-on:click="device = 'phone'" :class="device === 'phone' ? 'bg-white shadow-sm dark:bg-zinc-600' : 'text-zinc-500'" class="rounded-md px-2.5 py-1">{{ __('Phone') }}</button>
                        </div>
                    </div>
                </div>

                <div
                    x-ref="frame"
                    class="relative mx-auto overflow-hidden rounded-xl border border-zinc-200 bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800"
                    :class="device === 'phone' ? 'max-w-[16rem]' : 'w-full'"
                    :style="`height: ${height * scale}px`"
                >
                    <iframe
                        x-ref="iframe"
                        wire:ignore
                        wire:key="preview-{{ $page }}"
                        src="{{ $previewUrl }}"
                        x-on:load="
                            paint();
                            {{-- Links inside the preview swap the page without a reload; paint that too --}}
                            $el.contentDocument?.addEventListener('livewire:navigated', () => paint());
                        "
                        title="{{ __('Preview of the site in the colours you picked') }}"
                        tabindex="-1"
                        class="absolute top-0 left-0 origin-top-left border-0 bg-white"
                        :style="`width: ${width}px; height: ${height}px; transform: scale(${scale})`"
                    ></iframe>
                </div>

                <flux:text class="text-xs">
                    {{ __('Scroll inside the preview to see the whole page.') }}
                    <flux:link :href="$previewUrl" target="_blank" class="text-xs">{{ __('Open the live site') }}</flux:link>
                </flux:text>
            </div>
        </aside>

        <form wire:submit="save" class="space-y-8 lg:order-1">
            <fieldset class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <legend class="px-1.5">
                    <flux:heading size="lg">{{ __('Ready-made palettes') }}</flux:heading>
                </legend>
                <flux:text class="-mt-1 mb-4">{{ __('One click fills in all three colours. You can fine-tune them below.') }}</flux:text>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($palette->presets() as $key => $preset)
                        <button
                            type="button"
                            wire:key="preset-{{ $key }}"
                            wire:click="usePreset('{{ $key }}')"
                            aria-pressed="{{ $activePreset === $key ? 'true' : 'false' }}"
                            @class([
                                'group overflow-hidden rounded-xl border text-left transition focus-visible:outline-2 focus-visible:outline-offset-2',
                                'border-zinc-900 ring-2 ring-zinc-900 dark:border-white dark:ring-white' => $activePreset === $key,
                                'border-zinc-200 hover:border-zinc-400 dark:border-zinc-700 dark:hover:border-zinc-500' => $activePreset !== $key,
                            ])
                        >
                            {{-- A tiny page in the palette: menu bar, heading, a button and a label --}}
                            <span class="block p-2.5" style="background: {{ $preset['canvas'] }}">
                                <span class="block h-2 rounded-full" style="background: {{ $preset['brand'] }}"></span>
                                <span class="mt-2 block h-1.5 w-3/4 rounded-full" style="background: {{ $preset['brand'] }}"></span>
                                <span class="mt-1 block h-1.5 w-1/2 rounded-full opacity-40" style="background: {{ $preset['brand'] }}"></span>
                                <span class="mt-2.5 flex items-center gap-1.5">
                                    <span class="block h-3 w-8 rounded-full" style="background: {{ $preset['brand'] }}"></span>
                                    <span class="block h-3 w-3 rounded-full" style="background: {{ $preset['accent'] }}"></span>
                                </span>
                            </span>
                            <span class="flex items-center justify-between gap-2 border-t border-zinc-200 bg-white px-2.5 py-1.5 text-xs font-medium text-zinc-800 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                                <span class="truncate">{{ __($preset['label']) }}</span>
                                @if ($activePreset === $key)
                                    <flux:icon.check-circle variant="micro" class="shrink-0" />
                                @endif
                            </span>
                        </button>
                    @endforeach
                </div>
            </fieldset>

            <fieldset class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <legend class="px-1.5">
                    <flux:heading size="lg">{{ __('Your colours') }}</flux:heading>
                </legend>
                <flux:text class="-mt-1 mb-4">{{ __('Click a swatch to open the colour picker, or type a colour code. The lighter and darker shades are worked out for you.') }}</flux:text>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($palette->roles() as $role => $info)
                        @php
                            $value = $colors[$role] ?? '';
                            $color = $picked[$role];
                            $original = $palette->original($role);
                        @endphp

                        <div wire:key="role-{{ $role }}" class="py-5 first:pt-0 last:pb-0">
                            <div class="flex items-start gap-4">
                                <label class="relative block size-14 shrink-0 cursor-pointer overflow-hidden rounded-xl shadow-sm ring-1 ring-black/10 focus-within:outline-2 focus-within:outline-offset-2 dark:ring-white/15" style="background: {{ $color }}">
                                    <span class="sr-only">{{ __('Pick the :colour', ['colour' => strtolower(__($info['label']))]) }}</span>
                                    <input type="color" wire:model.live.debounce.150ms="colors.{{ $role }}" value="{{ $color }}" class="absolute inset-0 size-full cursor-pointer opacity-0">
                                    <span class="pointer-events-none absolute right-1 bottom-1 grid size-5 place-items-center rounded-full bg-white/90 text-zinc-700 shadow-sm">
                                        <flux:icon.eye-dropper variant="micro" class="size-3" />
                                    </span>
                                </label>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1">
                                        <flux:heading>{{ __($info['label']) }}</flux:heading>
                                        @if ($color !== $original)
                                            <button type="button" wire:click="useOriginal('{{ $role }}')" class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-600 underline underline-offset-2 hover:no-underline dark:text-zinc-300">
                                                <span class="inline-block size-3 rounded-full ring-1 ring-black/10" style="background: {{ $original }}"></span>
                                                {{ __('Use the original') }}
                                            </button>
                                        @endif
                                    </div>
                                    <flux:text class="text-sm">{{ __($info['help']) }}</flux:text>

                                    <div class="mt-2.5 max-w-44">
                                        <flux:input
                                            wire:model.live.debounce.500ms="colors.{{ $role }}"
                                            size="sm"
                                            :aria-label="__(':colour code', ['colour' => __($info['label'])])"
                                            placeholder="#1f3d2b"
                                            maxlength="7"
                                            spellcheck="false"
                                            class="font-mono"
                                        />
                                    </div>
                                    <flux:error name="colors.{{ $role }}" />
                                </div>
                            </div>

                            {{-- Every shade the site gets from this colour, lightest to darkest --}}
                            <div class="mt-3 flex overflow-hidden rounded-lg ring-1 ring-black/5 dark:ring-white/10" aria-hidden="true">
                                @foreach ($shades[$role] as $name => $shade)
                                    @continue(! str_starts_with($name, '--color-'.$info['family'].'-'))
                                    <span
                                        @class(['h-6 flex-1', 'relative z-10 -my-0.5 rounded-sm ring-2 ring-zinc-900 dark:ring-white' => $name === '--color-'.$info['family'].'-'.$info['anchor']])
                                        style="background: {{ $shade }}"
                                        title="{{ $shade }}"
                                    ></span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </fieldset>

            <fieldset class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <legend class="px-1.5">
                    <flux:heading size="lg">{{ __('Easy to read?') }}</flux:heading>
                </legend>
                <flux:text class="-mt-1 mb-4">{{ __('Some colours look lovely together but are hard to read. These checks use the standard web accessibility guidelines. You can still save if one fails.') }}</flux:text>

                <ul class="space-y-3">
                    @foreach ($checks as $check)
                        <li class="flex items-start gap-3">
                            @if ($check['ok'])
                                <flux:icon.check-circle variant="mini" class="mt-0.5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                            @else
                                <flux:icon.exclamation-triangle variant="mini" class="mt-0.5 shrink-0 text-amber-500" />
                            @endif
                            <div class="min-w-0">
                                <flux:text class="text-sm font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ $check['label'] }}
                                    <span class="ms-1 font-normal text-zinc-500">{{ $check['ok'] ? __('Easy to read') : __('Hard to read') }} ({{ $check['ratio'] }}:1)</span>
                                </flux:text>
                                @unless ($check['ok'])
                                    <flux:text class="text-sm">{{ $check['advice'] }}</flux:text>
                                @endunless
                            </div>
                        </li>
                    @endforeach
                </ul>
            </fieldset>

            @if ($isCustom)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-dashed border-zinc-300 p-4 dark:border-zinc-600">
                    <flux:text class="text-sm">{{ __('Want the colours the site started with?') }}</flux:text>
                    <flux:button
                        size="sm"
                        icon="arrow-path"
                        wire:click="restoreOriginal"
                        wire:confirm="{{ __('Put the original colours back on the site now?') }}"
                    >{{ __('Restore the original colours') }}</flux:button>
                </div>
            @endif

            {{-- Save stays in reach however long the page is --}}
            <div class="sticky bottom-0 z-10 -mx-2 flex items-center justify-between gap-4 border-t border-zinc-200 bg-white/95 px-2 py-3 backdrop-blur dark:border-zinc-700 dark:bg-zinc-800/95">
                <flux:text class="text-sm">
                    @if ($hasChanges)
                        <span class="text-amber-600 dark:text-amber-400">{{ __('You have unsaved changes. Only the preview shows them.') }}</span>
                    @else
                        {{ __('These are the colours on the site now.') }}
                    @endif
                </flux:text>

                <div class="flex items-center gap-2">
                    @if ($hasChanges)
                        <flux:button variant="ghost" wire:click="discard">{{ __('Discard') }}</flux:button>
                    @endif
                    <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">{{ __('Save colours') }}</span>
                        <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
                    </flux:button>
                </div>
            </div>
        </form>
    </div>
</section>
