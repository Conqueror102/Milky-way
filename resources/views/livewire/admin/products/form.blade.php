<section class="w-full max-w-2xl space-y-6">
    <div>
        <flux:link :href="route('admin.products.index')" wire:navigate class="text-sm">{{ __('Back to products') }}</flux:link>
        <flux:heading size="xl" level="1" class="mt-2">{{ $product ? __('Edit product') : __('New product') }}</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <flux:input wire:model.blur="name" :label="__('Name')" required />

        <flux:input wire:model="slug" :label="__('Link name')" :description="__('Used in the product page address. Letters, numbers and dashes only.')" required />

        <flux:textarea wire:model="description" :label="__('Description')" rows="5" />

        <div class="grid gap-6 sm:grid-cols-2">
            <flux:input wire:model="price" :label="__('Price (₦)')" :description="__('Leave empty to show Price on request.')" type="number" min="0" step="1" />
            <flux:input wire:model="stock" :label="__('Stock')" type="number" min="0" step="1" required />
        </div>

        <flux:field>
            <flux:label>{{ __('Photo') }}</flux:label>

            <div class="flex items-center gap-4">
                @if ($photo && $photo->isPreviewable())
                    <img src="{{ $photo->temporaryUrl() }}" alt="" class="size-24 rounded-lg object-cover">
                @elseif ($product?->image_url)
                    <img src="{{ $product->image_url }}" alt="" class="size-24 rounded-lg object-cover">
                @else
                    <div class="size-24 rounded-lg bg-zinc-100 dark:bg-zinc-700"></div>
                @endif

                <input type="file" wire:model="photo" accept="image/*" class="text-sm">
            </div>

            <div wire:loading wire:target="photo" class="text-sm text-zinc-500">{{ __('Uploading...') }}</div>
            <flux:error name="photo" />
        </flux:field>

        <flux:switch wire:model="is_active" :label="__('Show in shop')" />

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="save,photo">
                {{ __('Save product') }}
            </flux:button>
        </div>
    </form>
</section>
