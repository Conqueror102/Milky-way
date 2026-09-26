<section class="w-full max-w-2xl space-y-6">
    <div>
        <flux:link :href="route('admin.categories.index')" wire:navigate class="text-sm">{{ __('Back to categories') }}</flux:link>
        <flux:heading size="xl" level="1" class="mt-2">{{ $category ? __('Edit category') : __('New category') }}</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <flux:input wire:model="name" :label="__('Name')" :description="$category ? __('Renaming also renames it on all of its products.') : null" required />

        <flux:textarea wire:model="description" :label="__('Description')" :description="__('A line shown with the category in the shop.')" rows="3" />

        <flux:field>
            <flux:label>{{ __('Photo') }}</flux:label>
            <flux:description>{{ __('With a photo, the category gets its own card in the shop.') }}</flux:description>

            <div class="flex items-center gap-4">
                @if ($photo && $photo->isPreviewable())
                    <img src="{{ $photo->temporaryUrl() }}" alt="" class="size-24 rounded-lg object-cover">
                @elseif ($category?->imageSrc() && ! $removePhoto)
                    <img src="{{ $category->imageSrc() }}" alt="" class="size-24 rounded-lg object-cover">
                @else
                    <div class="size-24 rounded-lg bg-zinc-100 dark:bg-zinc-700"></div>
                @endif

                <div class="space-y-2">
                    <input type="file" wire:model="photo" accept="image/*" class="text-sm">
                    @if ($category?->imageSrc())
                        <flux:checkbox wire:model.live="removePhoto" :label="__('Remove the photo')" />
                    @endif
                </div>
            </div>

            <div wire:loading wire:target="photo" class="text-sm text-zinc-500">{{ __('Uploading...') }}</div>
            <flux:error name="photo" />
        </flux:field>

        <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="save,photo">
            {{ __('Save category') }}
        </flux:button>
    </form>
</section>
