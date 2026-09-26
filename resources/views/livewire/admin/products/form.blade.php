<section class="w-full max-w-2xl space-y-6">
    <div>
        <flux:link :href="route('admin.products.index')" wire:navigate class="text-sm">{{ __('Back to products') }}</flux:link>
        <flux:heading size="xl" level="1" class="mt-2">{{ $product ? __('Edit product') : __('New product') }}</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <flux:input wire:model.blur="name" :label="__('Name')" required />

        <flux:input wire:model="slug" :label="__('Link name')" :description="__('Used in the product page address. Letters, numbers and dashes only.')" required />

        <div class="grid gap-6 sm:grid-cols-2">
            <flux:select wire:model="category" :label="__('Category')" required>
                <flux:select.option value="">{{ __('Choose a category') }}</flux:select.option>
                @foreach ($this->categories as $option)
                    <flux:select.option :value="$option">{{ $option }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input wire:model="type" :label="__('Type')" :description="__('Optional, for example Serum or Soap.')" />
        </div>

        <div class="-mt-4">
            <flux:link :href="route('admin.categories.index')" wire:navigate class="text-sm">{{ __('Manage categories') }}</flux:link>
        </div>

        <flux:textarea wire:model="description" :label="__('Description')" rows="5" />

        <div class="grid gap-6 sm:grid-cols-2">
            <flux:input wire:model="price" :label="__('Price (₦)')" :description="__('Leave empty to show Price on request.')" type="number" min="0" step="1" />
            <flux:input wire:model="stock" :label="__('Stock')" :description="__('Leave empty if you don\'t track stock. At 0 it shows as sold out.')" type="number" min="0" step="1" />
        </div>

        <flux:field>
            <flux:label>{{ __('Main photo') }}</flux:label>

            <div class="flex items-center gap-4">
                @if ($photo && $photo->isPreviewable())
                    <img src="{{ $photo->temporaryUrl() }}" alt="" class="size-24 rounded-lg object-cover">
                @elseif ($product?->imageSrc())
                    <img src="{{ $product->imageSrc() }}" alt="" class="size-24 rounded-lg object-cover">
                @else
                    <div class="size-24 rounded-lg bg-zinc-100 dark:bg-zinc-700"></div>
                @endif

                <input type="file" wire:model="photo" accept="image/*" class="text-sm">
            </div>

            <div wire:loading wire:target="photo" class="text-sm text-zinc-500">{{ __('Uploading...') }}</div>
            <flux:error name="photo" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('More photos') }}</flux:label>
            <flux:description>{{ __('Shown after the main photo in the product page gallery. Up to 10 at a time.') }}</flux:description>

            @if ($this->galleryImages->isNotEmpty())
                <ul class="mt-2 flex flex-wrap gap-3">
                    @foreach ($this->galleryImages as $image)
                        <li wire:key="gallery-{{ $image->id }}" class="flex flex-col items-center gap-1">
                            <img src="{{ $image->url }}" alt="" class="size-24 rounded-lg object-cover">
                            <div class="flex">
                                <flux:button size="xs" variant="ghost" icon="chevron-left" wire:click="moveImage({{ $image->id }}, -1)" :disabled="$loop->first" :aria-label="__('Move earlier')" />
                                <flux:button size="xs" variant="ghost" icon="trash" wire:click="deleteImage({{ $image->id }})" wire:confirm="{{ __('Delete this photo?') }}" :aria-label="__('Delete photo')" />
                                <flux:button size="xs" variant="ghost" icon="chevron-right" wire:click="moveImage({{ $image->id }}, 1)" :disabled="$loop->last" :aria-label="__('Move later')" />
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($photos)
                <ul class="mt-2 flex flex-wrap gap-3">
                    @foreach ($photos as $index => $upload)
                        <li wire:key="upload-{{ $index }}" class="flex flex-col items-center gap-1">
                            @if ($upload->isPreviewable())
                                <img src="{{ $upload->temporaryUrl() }}" alt="" class="size-24 rounded-lg object-cover opacity-80">
                            @endif
                            <flux:button size="xs" variant="ghost" icon="x-mark" wire:click="removeUpload({{ $index }})">{{ __('Remove') }}</flux:button>
                        </li>
                    @endforeach
                </ul>
            @endif

            <input type="file" wire:model="photos" accept="image/*" multiple class="mt-2 text-sm">
            <div wire:loading wire:target="photos" class="text-sm text-zinc-500">{{ __('Uploading...') }}</div>
            <flux:error name="photos" />
            <flux:error name="photos.*" />
        </flux:field>

        <flux:switch wire:model="is_active" :label="__('Show in shop')" />

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="save,photo,photos">
                {{ __('Save product') }}
            </flux:button>
        </div>
    </form>
</section>
