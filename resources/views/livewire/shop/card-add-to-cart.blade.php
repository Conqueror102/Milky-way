<div x-data="{ flash: false }" class="flex shrink-0 self-stretch">
    <button
        type="button"
        x-on:click.stop="$wire.add().then(() => { if ($wire.added) { flash = true; setTimeout(() => flash = false, 1600) } })"
        wire:loading.attr="disabled"
        wire:target="add"
        title="{{ $problem ?? 'Add to cart' }}"
        class="grid aspect-square h-full place-items-center rounded-full bg-gold-500 text-white transition duration-200 hover:bg-cosmic-800 disabled:opacity-60"
    >
        <span class="sr-only">Add {{ $product->name }} to cart</span>
        <x-icon x-show="! flash" name="shopping-bag-solid" class="size-5" />
        <x-icon x-show="flash" x-cloak name="check-solid" class="size-5" />
    </button>
    <span class="sr-only" role="status">{{ $added ? 'Added to your cart' : $problem }}</span>
</div>
