<section class="w-full max-w-3xl">
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Delivery') }}</flux:heading>
        <flux:subheading>{{ __('The places you deliver to and what delivery costs in each. Shoppers pick one at checkout and pay the fee with their order.') }}</flux:subheading>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" class="mb-6" />
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="hidden grid-cols-[minmax(0,1fr)_11rem_7rem_5.5rem] gap-3 border-b border-zinc-200 bg-zinc-50 px-4 py-2 text-xs font-medium text-zinc-500 sm:grid dark:border-zinc-700 dark:bg-zinc-800/60">
                <span>{{ __('Place') }}</span>
                <span>{{ __('Delivery fee') }}</span>
                <span>{{ __('On home page') }}</span>
                <span class="sr-only">{{ __('Actions') }}</span>
            </div>

            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($places as $index => $place)
                    <div wire:key="place-{{ $place['id'] ?? 'new-'.$index }}" class="grid grid-cols-2 items-start gap-3 p-4 sm:grid-cols-[minmax(0,1fr)_11rem_7rem_5.5rem]">
                        <div class="col-span-2 sm:col-span-1">
                            <flux:input
                                wire:model="places.{{ $index }}.name"
                                :aria-label="__('Place name')"
                                :placeholder="__('e.g. Port Harcourt, or London, UK')"
                            />
                            <flux:error name="places.{{ $index }}.name" />
                        </div>

                        <div>
                            <flux:input
                                wire:model="places.{{ $index }}.fee"
                                type="number"
                                min="0"
                                step="1"
                                :aria-label="__('Delivery fee in naira')"
                                :placeholder="__('Arrange later')"
                            >
                                <x-slot name="iconLeading">
                                    <span class="text-sm text-zinc-500">₦</span>
                                </x-slot>
                            </flux:input>
                            <flux:error name="places.{{ $index }}.fee" />
                        </div>

                        <div class="flex h-10 items-center">
                            <flux:checkbox wire:model="places.{{ $index }}.featured" :label="__('Show')" />
                        </div>

                        <div class="col-span-2 flex h-10 items-center justify-end gap-0.5 sm:col-span-1">
                            <flux:button size="sm" variant="ghost" icon="chevron-up" wire:click="movePlace({{ $index }}, -1)" :disabled="$loop->first" :aria-label="__('Move up')" />
                            <flux:button size="sm" variant="ghost" icon="chevron-down" wire:click="movePlace({{ $index }}, 1)" :disabled="$loop->last" :aria-label="__('Move down')" />
                            <flux:button size="sm" variant="ghost" icon="trash" wire:click="removePlace({{ $index }})" :aria-label="__('Remove :place', ['place' => $place['name'] ?: __('this place')])" />
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center">
                        <flux:text>{{ __('No places yet. Shoppers can still choose "Somewhere else" at checkout.') }}</flux:text>
                    </div>
                @endforelse
            </div>

            <div class="border-t border-zinc-200 p-3 dark:border-zinc-700">
                <flux:button size="sm" icon="plus" wire:click="addPlace">{{ __('Add a place') }}</flux:button>
            </div>
        </div>

        <flux:error name="places" />

        <flux:text class="space-y-1 text-sm">
            <span class="block">{{ __('Add a state, a city, an area of Lagos or another country. The order here is the order shoppers see.') }}</span>
            <span class="block">{{ __('Enter 0 for free delivery. Leave the fee empty to agree it with the customer after they pay.') }}</span>
            <span class="block">{{ __('Tick "Show" on up to :count places to draw them on the home page. Shoppers outside every place can choose "Somewhere else" and you arrange delivery with them.', ['count' => \App\Models\DeliveryArea::MAX_FEATURED]) }}</span>
        </flux:text>

        <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">{{ __('Save delivery places') }}</span>
            <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
        </flux:button>
    </form>
</section>
