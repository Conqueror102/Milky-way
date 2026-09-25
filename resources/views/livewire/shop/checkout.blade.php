@php
    $field = 'font-sub mt-1.5 block w-full rounded-2xl border-0 bg-white px-4 py-3 text-sm text-cosmic-900 ring-1 ring-cosmic-900/15 placeholder:text-cosmic-900/35 focus:ring-2 focus:ring-cosmic-900 focus:outline-none';
    $label = 'font-sub text-sm font-semibold text-cosmic-900';
    $error = 'font-sub mt-1.5 text-sm text-red-700';
@endphp

<div class="mx-auto max-w-7xl px-6 pt-28 pb-16 sm:px-8 lg:pt-36 lg:pb-24">
    <a href="{{ route('cart') }}" class="font-sub inline-flex items-center gap-2 text-sm font-medium text-cosmic-900/65 transition hover:text-cosmic-900">
        <x-icon name="arrow-left-solid" class="size-4" />
        Back to cart
    </a>

    <x-shop.page-heading eyebrow="Checkout" class="mt-6">
        Where should we <span class="font-script tracking-[-0.03em] text-gold-500">deliver</span>?
    </x-shop.page-heading>

    <p class="font-sub mt-4 max-w-xl text-base leading-relaxed text-cosmic-900/70">
        Place your order and we will call or WhatsApp you to confirm availability, delivery cost and payment. You pay nothing online.
    </p>

    <form wire:submit="placeOrder" class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-start">
        <div class="grid gap-5 rounded-[1.5rem] bg-white p-5 ring-1 ring-cosmic-900/8 sm:grid-cols-2 sm:p-6">
            <div class="sm:col-span-2">
                <label for="customer_name" class="{{ $label }}">Full name</label>
                <input id="customer_name" type="text" wire:model="customer_name" autocomplete="name" required class="{{ $field }}" />
                @error('customer_name') <p class="{{ $error }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="customer_phone" class="{{ $label }}">Phone or WhatsApp number</label>
                <input id="customer_phone" type="tel" wire:model="customer_phone" autocomplete="tel" required placeholder="+234 800 000 0000" class="{{ $field }}" />
                @error('customer_phone') <p class="{{ $error }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="customer_email" class="{{ $label }}">Email <span class="font-normal text-cosmic-900/50">(optional)</span></label>
                <input id="customer_email" type="email" wire:model="customer_email" autocomplete="email" class="{{ $field }}" />
                @error('customer_email') <p class="{{ $error }}">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="delivery_area" class="{{ $label }}">Delivery area</label>
                <select id="delivery_area" wire:model="delivery_area" required class="{{ $field }}">
                    <option value="">Choose an area</option>
                    @foreach (\App\Livewire\Shop\Checkout::deliveryAreas() as $area)
                        <option value="{{ $area }}">{{ $area }}</option>
                    @endforeach
                </select>
                @error('delivery_area') <p class="{{ $error }}">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="delivery_address" class="{{ $label }}">Delivery address</label>
                <textarea id="delivery_address" wire:model="delivery_address" rows="3" autocomplete="street-address" required class="{{ $field }}"></textarea>
                @error('delivery_address') <p class="{{ $error }}">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="notes" class="{{ $label }}">Order notes <span class="font-normal text-cosmic-900/50">(optional)</span></label>
                <textarea id="notes" wire:model="notes" rows="2" placeholder="Landmarks, preferred delivery time, anything else" class="{{ $field }}"></textarea>
                @error('notes') <p class="{{ $error }}">{{ $message }}</p> @enderror
            </div>
        </div>

        <aside class="rounded-[1.5rem] bg-cosmic-950 p-6 text-cream-50">
            <h2 class="text-xl font-bold">Your order</h2>

            <ul class="font-sub mt-5 grid gap-3 text-sm">
                @foreach ($this->lines as $line)
                    <li wire:key="summary-{{ $line->product->id }}" class="flex justify-between gap-4">
                        <span class="text-cream-50/80">{{ $line->quantity }} &times; {{ $line->product->name }}</span>
                        <span class="shrink-0 font-semibold">{{ \App\Support\Money::format($line->total()) }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="font-sub mt-5 flex justify-between border-t border-cream-50/15 pt-4 text-base">
                <span>Subtotal</span>
                <span class="font-bold">{{ \App\Support\Money::format($this->subtotal) }}</span>
            </div>
            <p class="font-sub mt-1 text-xs text-cream-50/60">Delivery is confirmed with you after ordering.</p>

            @error('cart') <p class="font-sub mt-4 rounded-xl bg-red-100 px-3 py-2 text-sm text-red-800">{{ $message }}</p> @enderror

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="placeOrder"
                class="font-sub mt-6 flex w-full items-center justify-center gap-2 rounded-full bg-cream-50 px-6 py-3.5 text-sm font-bold text-cosmic-900 transition hover:bg-white disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="placeOrder">Place order</span>
                <span wire:loading wire:target="placeOrder">Placing order…</span>
            </button>
        </aside>
    </form>
</div>
