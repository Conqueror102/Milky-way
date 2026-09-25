<section class="w-full space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Products') }}</flux:heading>
            <flux:subheading>{{ __('Add, edit and remove what shows in the shop.') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('admin.products.create')" wire:navigate>
            {{ __('New product') }}
        </flux:button>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
    @endif

    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search products')" class="max-w-sm" />

    <flux:table :paginate="$this->products">
        <flux:table.columns>
            <flux:table.column>{{ __('Product') }}</flux:table.column>
            <flux:table.column>{{ __('Price') }}</flux:table.column>
            <flux:table.column>{{ __('Stock') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->products as $product)
                <flux:table.row :key="$product->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            @if ($product->imageSrc())
                                <img src="{{ $product->imageSrc() }}" alt="" class="size-10 rounded-md object-cover" loading="lazy">
                            @else
                                <div class="size-10 rounded-md bg-zinc-100 dark:bg-zinc-700"></div>
                            @endif
                            <span class="font-medium">{{ $product->name }}</span>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $product->price === null ? __('On request') : '₦'.number_format($product->price) }}</flux:table.cell>
                    <flux:table.cell>{{ $product->stock }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($product->is_active)
                            <flux:badge color="green" size="sm">{{ __('Live') }}</flux:badge>
                        @else
                            <flux:badge color="zinc" size="sm">{{ __('Hidden') }}</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button size="sm" variant="ghost" icon="pencil-square" :href="route('admin.products.edit', $product)" wire:navigate>
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:button
                                size="sm"
                                variant="ghost"
                                icon="trash"
                                wire:click="delete({{ $product->id }})"
                                wire:confirm="{{ __('Delete :name? This cannot be undone.', ['name' => $product->name]) }}"
                            >
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center">{{ __('No products yet.') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
