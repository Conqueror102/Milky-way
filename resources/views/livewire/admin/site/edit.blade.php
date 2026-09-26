<section class="w-full">
    <div class="mb-6">
        <flux:link :href="route('admin.site.index')" wire:navigate class="text-sm">{{ __('All sections') }}</flux:link>
        <flux:heading size="xl" level="1" class="mt-2">{{ __($info['label']) }}</flux:heading>
        <flux:subheading>{{ __($info['description']) }}</flux:subheading>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" class="mb-6" />
    @endif

    <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,26rem)] xl:grid-cols-[minmax(0,1fr)_minmax(0,32rem)]">

        {{-- The preview comes first on phones, so the admin sees the section before the fields --}}
        <aside class="lg:order-2">
            <div class="space-y-3 lg:sticky lg:top-6">
                @if ($previewUrl)
                    <div
                        x-data="{
                            device: 'desktop',
                            scale: 0.3,
                            get width() { return this.device === 'desktop' ? 1280 : 390 },
                            get height() { return this.device === 'desktop' ? 860 : 780 },
                            fit() { this.scale = Math.min(1, this.$refs.frame.clientWidth / this.width) },
                        }"
                        x-init="fit(); $watch('device', () => $nextTick(() => fit()))"
                        x-on:resize.window.debounce.100ms="fit()"
                    >
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <flux:heading size="sm">{{ __('Live preview') }}</flux:heading>
                            <div class="flex items-center gap-1 rounded-lg bg-zinc-100 p-0.5 text-xs font-medium dark:bg-zinc-800">
                                <button type="button" x-on:click="device = 'desktop'" :class="device === 'desktop' ? 'bg-white shadow-sm dark:bg-zinc-600' : 'text-zinc-500'" class="rounded-md px-2.5 py-1">{{ __('Computer') }}</button>
                                <button type="button" x-on:click="device = 'phone'" :class="device === 'phone' ? 'bg-white shadow-sm dark:bg-zinc-600' : 'text-zinc-500'" class="rounded-md px-2.5 py-1">{{ __('Phone') }}</button>
                            </div>
                        </div>

                        <div
                            x-ref="frame"
                            class="relative mx-auto overflow-hidden rounded-xl border border-zinc-200 bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800"
                            :class="device === 'phone' ? 'max-w-[16rem]' : 'w-full'"
                            :style="`height: ${height * scale}px`"
                        >
                            <iframe
                                wire:key="preview-{{ $version }}-{{ $section }}"
                                src="{{ route('home', ['preview' => $version]) }}"
                                x-on:load="
                                    {{-- Scroll inside the frame; a #fragment would scroll this page too --}}
                                    const doc = $el.contentDocument;
                                    const target = doc?.getElementById(@js($info['anchor']));
                                    if (target) {
                                        if (@js($info['anchor']) !== 'top') doc.querySelectorAll('header').forEach(h => h.style.visibility = 'hidden');
                                        $el.contentWindow.scrollTo(0, target.getBoundingClientRect().top + $el.contentWindow.scrollY);
                                    }
                                "
                                title="{{ __('Preview of the :section section', ['section' => $info['label']]) }}"
                                loading="lazy"
                                tabindex="-1"
                                class="pointer-events-none absolute top-0 left-0 origin-top-left border-0 bg-white"
                                :style="`width: ${width}px; height: ${height}px; transform: scale(${scale})`"
                            ></iframe>
                        </div>

                        <flux:text class="mt-2 text-xs">
                            {{ __('Shows the site as it is now. It updates when you save.') }}
                            <flux:link :href="$previewUrl" target="_blank" class="text-xs">{{ __('Open on the site') }}</flux:link>
                        </flux:text>
                    </div>
                @endif
            </div>
        </aside>

        <form wire:submit="save" class="space-y-8 lg:order-1">
            @foreach ($info['groups'] as $group)
                <fieldset class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                    <legend class="px-1.5">
                        <flux:heading size="lg">{{ __($group['label']) }}</flux:heading>
                    </legend>
                    @if (! empty($group['description']))
                        <flux:text class="-mt-1 mb-4">{{ __($group['description']) }}</flux:text>
                    @endif

                    <div class="space-y-5">
                        @foreach ($group['fields'] as $name => $field)
                            @php $key = $section.'.'.$name; @endphp

                            @if ($field['type'] === 'image')
                                @php
                                    $upload = $photos[$name] ?? null;
                                    $restoring = ! empty($restore[$name]);
                                    $custom = $content->image($key) !== null;

                                    [$src, $badge, $badgeColor] = match (true) {
                                        $upload !== null && $upload->isPreviewable() => [$upload->temporaryUrl(), __('New, not saved yet'), 'amber'],
                                        $restoring => [$field['default'], __('Original, on save'), 'amber'],
                                        $custom => [\App\Support\SiteContent::resized($content->imageUrl($key), 480), __('Your photo'), 'green'],
                                        default => [$field['default'], __('Original'), 'zinc'],
                                    };
                                @endphp

                                <div wire:key="field-{{ $name }}" class="flex gap-4">
                                    <div class="relative w-28 shrink-0 sm:w-44">
                                        <img src="{{ $src }}" alt="" class="aspect-[4/3] w-full rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700">
                                        <span @class([
                                            'absolute top-1.5 left-1.5 rounded-full px-2 py-0.5 text-[0.7rem] font-semibold shadow-sm',
                                            'bg-zinc-900/80 text-white' => $badgeColor === 'zinc',
                                            'bg-emerald-600 text-white' => $badgeColor === 'green',
                                            'bg-amber-400 text-zinc-900' => $badgeColor === 'amber',
                                        ])>{{ $badge }}</span>
                                        <div wire:loading.flex wire:target="photos.{{ $name }}" class="absolute inset-0 items-center justify-center rounded-lg bg-white/80 text-sm font-medium text-zinc-700 dark:bg-zinc-900/80 dark:text-zinc-200">
                                            {{ __('Uploading…') }}
                                        </div>
                                    </div>

                                    <div class="min-w-0 flex-1 space-y-2">
                                        <flux:label>{{ __($field['label']) }}</flux:label>
                                        @if (! empty($field['help']))
                                            <flux:text class="text-sm">{{ __($field['help']) }}</flux:text>
                                        @endif

                                        <div class="flex flex-wrap items-center gap-2">
                                            <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-sm font-medium text-zinc-800 shadow-xs hover:bg-zinc-50 focus-within:outline-2 focus-within:outline-offset-2 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:hover:bg-zinc-700">
                                                <flux:icon.photo variant="micro" />
                                                {{ $custom || $upload ? __('Replace photo') : __('Choose a photo') }}
                                                <input type="file" wire:model="photos.{{ $name }}" accept="image/*" class="sr-only">
                                            </label>

                                            @if ($upload)
                                                <flux:button size="sm" variant="ghost" icon="x-mark" wire:click="discardPhoto('{{ $name }}')">{{ __('Cancel') }}</flux:button>
                                            @elseif ($restoring)
                                                <flux:button size="sm" variant="ghost" icon="arrow-uturn-left" wire:click="undoRestore('{{ $name }}')">{{ __('Keep my photo') }}</flux:button>
                                            @elseif ($custom)
                                                <flux:button size="sm" variant="ghost" icon="arrow-path" wire:click="restorePhoto('{{ $name }}')">{{ __('Use the original') }}</flux:button>
                                            @endif
                                        </div>

                                        @if (($custom || $upload) && ! $restoring)
                                            <flux:input
                                                wire:model.live.debounce.400ms="alts.{{ $name }}"
                                                size="sm"
                                                :placeholder="__('For example: A woman applying face cream')"
                                                :label="__('Describe the photo')"
                                                :description="__('Read out by screen readers and used by Google. Optional.')"
                                            />
                                        @endif

                                        <flux:error name="photos.{{ $name }}" />
                                    </div>
                                </div>
                            @else
                                @php
                                    $default = (string) $field['default'];
                                    $changed = \App\Support\SiteContent::splitLines($values[$name] ?? '') !== \App\Support\SiteContent::splitLines($default);
                                @endphp

                                <div wire:key="field-{{ $name }}">
                                    @if ($field['type'] === 'text')
                                        <flux:input
                                            wire:model.live.debounce.400ms="values.{{ $name }}"
                                            :label="__($field['label'])"
                                            :description="! empty($field['help']) ? __($field['help']) : null"
                                            :placeholder="$default"
                                        />
                                    @else
                                        <flux:textarea
                                            wire:model.live.debounce.400ms="values.{{ $name }}"
                                            :label="__($field['label'])"
                                            :description="! empty($field['help']) ? __($field['help']) : ($field['type'] === 'lines' ? __('One per line.') : null)"
                                            :placeholder="$default"
                                            :rows="$field['type'] === 'lines' ? max(3, count(\App\Support\SiteContent::splitLines($values[$name] ?? '')) + 1) : 3"
                                        />
                                    @endif

                                    @if ($changed)
                                        <div class="mt-1.5 flex flex-wrap items-baseline gap-x-2 text-xs text-zinc-500">
                                            <span class="line-clamp-1">{{ __('Original:') }} “{{ \Illuminate\Support\Str::limit(str_replace("\n", ' · ', $default), 90) }}”</span>
                                            <button type="button" wire:click="useDefault('{{ $name }}')" class="font-medium text-zinc-800 underline underline-offset-2 hover:no-underline dark:text-zinc-200">{{ __('Use the original') }}</button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </fieldset>
            @endforeach

            {{-- Save stays in reach however long the section is --}}
            <div class="sticky bottom-0 z-10 -mx-2 flex items-center justify-between gap-4 border-t border-zinc-200 bg-white/95 px-2 py-3 backdrop-blur dark:border-zinc-700 dark:bg-zinc-800/95">
                <flux:text class="text-sm">
                    @if ($hasChanges)
                        <span class="text-amber-600 dark:text-amber-400">{{ __('You have unsaved changes.') }}</span>
                    @else
                        {{ __('Everything here is live on the site.') }}
                    @endif
                </flux:text>

                <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="save,photos">
                    <span wire:loading.remove wire:target="save">{{ __('Save changes') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
                </flux:button>
            </div>
        </form>
    </div>
</section>
