<div class="mx-auto max-w-7xl px-6 pt-28 pb-16 sm:px-8 lg:pt-36 lg:pb-24">
    <x-shop.page-heading eyebrow="Shop">
        All our <span class="font-script tracking-[-0.03em] text-gold-500">products</span>
    </x-shop.page-heading>

    <div class="mt-8 flex flex-wrap gap-2" role="group" aria-label="Filter by category">
        <button
            type="button"
            wire:click="show('')"
            @class([
                'font-sub rounded-full px-4 py-2 text-xs font-semibold transition duration-150',
                'bg-cosmic-900 text-cream-50' => $category === '',
                'bg-white text-cosmic-900/75 ring-1 ring-cosmic-900/8 hover:text-cosmic-900' => $category !== '',
            ])
            @if ($category === '') aria-pressed="true" @endif
        >All</button>

        @foreach ($this->categories as $name)
            <button
                type="button"
                wire:key="filter-{{ $loop->index }}"
                wire:click="show(@js($name))"
                @class([
                    'font-sub rounded-full px-4 py-2 text-xs font-semibold transition duration-150',
                    'bg-cosmic-900 text-cream-50' => $category === $name,
                    'bg-white text-cosmic-900/75 ring-1 ring-cosmic-900/8 hover:text-cosmic-900' => $category !== $name,
                ])
                @if ($category === $name) aria-pressed="true" @endif
            >{{ $name }}</button>
        @endforeach
    </div>

    <p class="font-sub mt-4 text-sm text-cosmic-900/60">
        {{ $this->products->count() }} {{ Str::plural('product', $this->products->count()) }}
    </p>

    @if ($this->products->isEmpty())
        <p class="font-sub mt-8 text-base text-cosmic-900/70">No products here yet. Check back soon.</p>
    @else
        <div class="mt-4 grid gap-3.5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($this->products as $product)
                <x-shop.product-card :product="$product" wire:key="product-{{ $product->id }}" />
            @endforeach
        </div>
    @endif
</div>
