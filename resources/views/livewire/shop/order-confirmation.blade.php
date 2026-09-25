<div class="mx-auto max-w-3xl px-6 pt-28 pb-16 sm:px-8 lg:pt-36 lg:pb-24">
    <div class="grid size-14 place-items-center rounded-full bg-gold-500 text-white shadow-lg shadow-gold-500/30">
        <x-icon name="check-solid" class="size-7" />
    </div>

    <x-shop.page-heading eyebrow="Order {{ $order->reference }}" class="mt-6">
        @if ($order->isPaid())
            Thank you, <span class="font-script tracking-[-0.03em] text-gold-500">{{ Str::before($order->customer_name, ' ') ?: $order->customer_name }}</span>
        @else
            One last <span class="font-script tracking-[-0.03em] text-gold-500">step</span>
        @endif
    </x-shop.page-heading>

    <p class="font-sub mt-4 text-base leading-relaxed text-cosmic-900/75">
        @if ($order->isPaid())
            Your payment is confirmed. We will get your order ready and contact you on <strong class="text-cosmic-900">{{ $order->customer_phone }}</strong> about delivery.
        @else
            Your order is saved. Pay for it below and we will get it ready for delivery.
        @endif
    </p>

    @if (session('payment_notice'))
        <p class="font-sub mt-6 rounded-xl bg-red-100 px-4 py-3 text-sm text-red-800" role="status">{{ session('payment_notice') }}</p>
    @endif

    {{-- Payment step --}}
    <div class="mt-8 rounded-[1.5rem] bg-cosmic-950 p-6 text-cream-50">
        <div class="font-sub flex items-center justify-between gap-4">
            <span class="text-cream-50/75">Amount to pay</span>
            <span class="text-2xl font-bold">{{ $order->formattedSubtotal() }}</span>
        </div>

        @if ($order->isPaid())
            <p class="font-sub mt-4 inline-flex items-center gap-2 text-sm font-semibold text-cream-50">
                <x-icon name="check-circle-solid" class="size-5 text-gold-400" />
                Paid
            </p>
        @elseif ($this->paymentsEnabled())
            <button
                type="button"
                wire:click="pay"
                wire:loading.attr="disabled"
                class="font-sub mt-6 flex w-full items-center justify-center gap-2 rounded-full bg-cream-50 px-6 py-3.5 text-sm font-bold text-cosmic-900 transition hover:bg-white disabled:opacity-60"
            >
                Pay {{ $order->formattedSubtotal() }}
            </button>
            @error('payment') <p class="font-sub mt-3 rounded-xl bg-red-100 px-3 py-2 text-sm text-red-800">{{ $message }}</p> @enderror
            <p class="font-sub mt-3 text-center text-xs text-cream-50/60">Secure card, bank transfer and USSD payment by Paystack.</p>
        @else
            <button
                type="button"
                disabled
                class="font-sub mt-6 flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-full bg-cream-50/20 px-6 py-3.5 text-sm font-bold text-cream-50/70"
            >
                Pay {{ $order->formattedSubtotal() }}
            </button>
            <p class="font-sub mt-3 text-center text-xs text-cream-50/60">Online payment is being set up. Your order is saved in the meantime.</p>
        @endif
    </div>

    <div class="mt-6 rounded-[1.5rem] bg-white p-5 ring-1 ring-cosmic-900/8 sm:p-6">
        <div class="font-sub flex flex-wrap items-center justify-between gap-2 text-sm">
            <h2 class="font-sans text-xl font-bold text-cosmic-900">Order summary</h2>
            <span class="rounded-full bg-cosmic-900/6 px-3 py-1 font-semibold text-cosmic-900/75">{{ $order->payment_status->label() }}</span>
        </div>

        <ul class="font-sub mt-5 grid gap-3 text-sm">
            @foreach ($order->items as $item)
                <li wire:key="item-{{ $item->id }}" class="flex justify-between gap-4">
                    <span class="text-cosmic-900/80">{{ $item->quantity }} &times; {{ $item->product_name }}</span>
                    <span class="shrink-0 font-semibold text-cosmic-900">{{ $item->formattedLineTotal() }}</span>
                </li>
            @endforeach
        </ul>

        <div class="font-sub mt-5 flex justify-between border-t border-cosmic-900/10 pt-4 text-base text-cosmic-900">
            <span>Subtotal</span>
            <span class="font-bold">{{ $order->formattedSubtotal() }}</span>
        </div>

        <dl class="font-sub mt-6 grid gap-4 text-sm sm:grid-cols-2">
            <div>
                <dt class="font-semibold text-cosmic-900">Deliver to</dt>
                <dd class="mt-1 whitespace-pre-line text-cosmic-900/70">{{ $order->delivery_address }}
{{ $order->delivery_area }}</dd>
            </div>
            <div>
                <dt class="font-semibold text-cosmic-900">Contact</dt>
                <dd class="mt-1 text-cosmic-900/70">{{ $order->customer_name }}<br>{{ $order->customer_phone }}@if ($order->customer_email)<br>{{ $order->customer_email }}@endif</dd>
            </div>
        </dl>
    </div>

    <a href="{{ route('home') }}#shop" class="font-sub mt-8 inline-flex items-center gap-2 text-sm font-medium text-cosmic-900/65 transition hover:text-cosmic-900">
        <x-icon name="arrow-left-solid" class="size-4" />
        Back to the shop
    </a>
</div>
