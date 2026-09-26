<section class="w-full space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Categories') }}</flux:heading>
            <flux:subheading>{{ __('The groups products are filed under in the shop, in the order shown here.') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('admin.categories.create')" wire:navigate>
            {{ __('New category') }}
        </flux:button>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Products') }}</flux:table.column>
            <flux:table.column>{{ __('Order') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->categories as $category)
                <flux:table.row :key="$category->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            @if ($category->imageSrc())
                                <img src="{{ $category->imageSrc() }}" alt="" class="size-10 rounded-md object-cover" loading="lazy">
                            @else
                                <div class="size-10 rounded-md bg-zinc-100 dark:bg-zinc-700"></div>
                            @endif
                            <div>
                                <div class="font-medium">{{ $category->name }}</div>
                                @if ($category->description)
                                    <div class="line-clamp-1 max-w-md text-xs text-zinc-500">{{ $category->description }}</div>
                                @endif
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $category->products_count }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex">
                            <flux:button size="xs" variant="ghost" icon="chevron-up" wire:click="move({{ $category->id }}, -1)" :disabled="$loop->first" :aria-label="__('Move up')" />
                            <flux:button size="xs" variant="ghost" icon="chevron-down" wire:click="move({{ $category->id }}, 1)" :disabled="$loop->last" :aria-label="__('Move down')" />
                        </div>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button size="sm" variant="ghost" icon="pencil-square" :href="route('admin.categories.edit', $category)" wire:navigate>
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete({{ $category->id }})">
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>

                @if ($deleting === $category->id)
                    <flux:table.row :key="'delete-'.$category->id">
                        <flux:table.cell colspan="4">
                            <form wire:submit="delete" class="flex flex-wrap items-end gap-4 rounded-lg bg-red-50 p-4 dark:bg-red-950/40">
                                <div class="flex-1 space-y-2">
                                    <flux:text class="font-medium">{{ __('Delete :name?', ['name' => $category->name]) }}</flux:text>

                                    @if ($category->products_count > 0)
                                        <flux:select wire:model="moveTo" :label="__('Move its :count products to', ['count' => $category->products_count])" class="max-w-xs">
                                            <flux:select.option value="">{{ __('Choose a category') }}</flux:select.option>
                                            @foreach ($this->categories->where('id', '!=', $category->id) as $other)
                                                <flux:select.option :value="$other->name">{{ $other->name }}</flux:select.option>
                                            @endforeach
                                        </flux:select>
                                    @else
                                        <flux:text>{{ __('It has no products.') }}</flux:text>
                                    @endif
                                </div>

                                <div class="flex gap-2">
                                    <flux:button type="button" variant="ghost" wire:click="cancelDelete">{{ __('Cancel') }}</flux:button>
                                    <flux:button type="submit" variant="danger">{{ __('Delete category') }}</flux:button>
                                </div>
                            </form>
                        </flux:table.cell>
                    </flux:table.row>
                @endif
            @endforeach
        </flux:table.rows>
    </flux:table>
</section>
