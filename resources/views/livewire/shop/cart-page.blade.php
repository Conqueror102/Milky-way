<div class="mx-auto max-w-7xl px-6 pt-28 pb-16 sm:px-8 lg:pt-36 lg:pb-24">
    <x-shop.page-heading eyebrow="Your cart">
        @if ($this->lines->isEmpty())
            Your cart is <span class="font-script tracking-[-0.03em] text-gold-500">empty</span>
        @else
            Ready when <span class="font-script tracking-[-0.03em] text-gold-500">you</span> are
        @endif
    </x-shop.page-heading>

    @if ($this->lines->isEmpty())
        <p class="font-sub mt-4 max-w-md text-base leading-relaxed text-cosmic-900/70">
            Browse the shop and add a few favourites. They will wait for you here.
        </p>

        <a href="{{ route('home') }}#shop" class="font-sub mt-8 inline-flex items-center gap-2 rounded-full bg-cosmic-900 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-cosmic-800">
            Browse products
            <x-icon name="arrow-right-solid" class="size-4" />
        </a>
    @else
        <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-start">
            <ul class="grid gap-3">
                @foreach ($this->lines as $line)
                    <li wire:key="line-{{ $line->product->id }}" class="flex gap-4 rounded-[1.5rem] bg-white p-3 ring-1 ring-cosmic-900/8 sm:items-center">
                        <a href="{{ route('products.show', $line->product) }}" class="shrink-0">
                            <x-shop.product-image :product="$line->product" class="size-20 rounded-[1rem] sm:size-24" />
                        </a>

                        <div class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <a href="{{ route('products.show', $line->product) }}" class="text-base leading-tight font-bold text-cosmic-900 hover:underline">{{ $line->product->name }}</a>
                                <p class="font-sub mt-1 text-sm text-cosmic-900/60">{{ $line->product->formattedPrice() }} each</p>
                            </div>

                            <div class="flex items-center justify-between gap-4 sm:justify-end">
                                <x-shop.quantity-stepper
                                    :quantity="$line->quantity"
                                    :decrement="'decrement('.$line->product->id.')'"
                                    :increment="'increment('.$line->product->id.')'"
                                    :label="'Quantity of '.$line->product->name"
                                />

                                <p class="font-sub w-24 text-right text-sm font-bold text-cosmic-900">{{ \App\Support\Money::format($line->total()) }}</p>

                                <button type="button" wire:click="remove({{ $line->product->id }})" class="grid size-9 place-items-center rounded-full text-cosmic-900/50 transition hover:bg-cosmic-900/6 hover:text-red-700">
                                    <span class="sr-only">Remove {{ $line->product->name }}</span>
                                    <x-icon name="trash-alt-solid" class="size-4" />
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <aside class="rounded-[1.5rem] bg-cosmic-950 p-6 text-cream-50">
                <h2 class="text-xl font-bold">Order summary</h2>

                <dl class="font-sub mt-5 grid gap-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-cream-50/70">Subtotal</dt>
                        <dd class="font-bold">{{ \App\Support\Money::format($this->subtotal) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-cream-50/70">Delivery</dt>
                        <dd class="text-cream-50/70">Confirmed with you after ordering</dd>
                    </div>
                </dl>

                <a href="{{ route('checkout') }}" class="font-sub mt-6 flex items-center justify-center gap-2 rounded-full bg-cream-50 px-6 py-3.5 text-sm font-bold text-cosmic-900 transition hover:bg-white">
                    Checkout
                    <x-icon name="arrow-right-solid" class="size-4" />
                </a>

                <a href="{{ route('home') }}#shop" class="font-sub mt-3 block text-center text-sm text-cream-50/70 underline-offset-4 hover:text-cream-50 hover:underline">Continue shopping</a>
            </aside>
        </div>
    @endif
</div>
