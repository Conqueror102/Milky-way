<section class="w-full max-w-2xl">
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Social links') }}</flux:heading>
        <flux:subheading>{{ __('Link your social accounts in the site footer. Tick Show for the accounts you have and untick the rest.') }}</flux:subheading>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" class="mb-6" />
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="divide-y divide-zinc-200 rounded-xl border border-zinc-200 dark:divide-zinc-700 dark:border-zinc-700">
            @foreach ($networks as $key => $network)
                <div wire:key="network-{{ $key }}" class="grid gap-3 p-4 sm:grid-cols-[8rem_minmax(0,1fr)_5rem] sm:items-start">
                    <div class="pt-2">
                        <flux:heading>{{ $network['label'] }}</flux:heading>
                    </div>

                    <div>
                        <flux:input
                            wire:model="networks.{{ $key }}.url"
                            type="url"
                            :aria-label="__(':network link', ['network' => $network['label']])"
                            :placeholder="$key === 'whatsapp' ? __('Leave empty to use your WhatsApp number') : 'https://'"
                        />
                        <flux:error name="networks.{{ $key }}.url" />
                    </div>

                    <div class="flex h-10 items-center">
                        <flux:checkbox wire:model="networks.{{ $key }}.show" :label="__('Show')" />
                    </div>
                </div>
            @endforeach
        </div>

        <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">{{ __('Save social links') }}</span>
            <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
        </flux:button>
    </form>
</section>
