@php
    $panel = 'rounded-xl border border-zinc-200 p-5 dark:border-zinc-700';
    $maxDay = max(1, $this->dailySales->max('revenue'));
    $monthTotal = $this->dailySales->sum('revenue');
@endphp

<section class="w-full space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading>{{ __('How the shop is doing, at a glance.') }}</flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:button size="sm" :href="route('admin.orders.index')" wire:navigate>{{ __('All orders') }}</flux:button>
            <flux:button size="sm" :href="route('admin.transactions.index')" wire:navigate>{{ __('Transactions') }}</flux:button>
        </div>
    </div>

    {{-- Money in --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($this->sales as $key => $period)
            <div class="{{ $panel }}" data-test="sales-{{ $key }}">
                <flux:text>{{ __('Sales :period', ['period' => strtolower($period['label'])]) }}</flux:text>
                <div class="mt-1 text-2xl font-semibold tabular-nums text-zinc-900 dark:text-white">₦{{ number_format($period['revenue']) }}</div>
                <flux:text class="mt-1 text-sm">{{ trans_choice(':count paid order|:count paid orders', $period['orders']) }}</flux:text>
            </div>
        @endforeach

        <div class="{{ $panel }}">
            <flux:text>{{ __('Average order, last 30 days') }}</flux:text>
            <div class="mt-1 text-2xl font-semibold tabular-nums text-zinc-900 dark:text-white">₦{{ number_format($this->averageOrderValue) }}</div>
            <flux:text class="mt-1 text-sm">{{ __('Paid orders only') }}</flux:text>
        </div>
    </div>

    {{-- Things that need doing --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" wire:navigate class="{{ $panel }} transition hover:border-zinc-400 dark:hover:border-zinc-500">
            <flux:text>{{ __('Paid, ready to send out') }}</flux:text>
            <div class="mt-1 text-2xl font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $this->ordersByStatus['confirmed'] }}</div>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" wire:navigate class="{{ $panel }} transition hover:border-zinc-400 dark:hover:border-zinc-500">
            <flux:text>{{ __('Waiting for payment') }}</flux:text>
            <div class="mt-1 text-2xl font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $this->awaitingPayment }}</div>
        </a>
        <a href="{{ route('admin.transactions.index', ['status' => 'failed']) }}" wire:navigate class="{{ $panel }} transition hover:border-zinc-400 dark:hover:border-zinc-500">
            <flux:text>{{ __('Failed payments, last 7 days') }}</flux:text>
            <div class="mt-1 text-2xl font-semibold tabular-nums text-zinc-900 dark:text-white">{{ $this->failedPaymentsThisWeek }}</div>
        </a>
    </div>

    {{-- Daily sales, last 30 days --}}
    <div class="{{ $panel }}">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <flux:heading>{{ __('Sales, last 30 days') }}</flux:heading>
            <flux:text class="tabular-nums">{{ __('Total :amount', ['amount' => '₦'.number_format($monthTotal)]) }}</flux:text>
        </div>

        <div class="mt-4 flex h-40 items-end gap-[2px] border-b border-zinc-200 dark:border-zinc-700" role="img" aria-label="{{ __('Daily sales for the last 30 days') }}">
            @foreach ($this->dailySales as $day)
                <div class="group relative flex h-full flex-1 items-end">
                    <div
                        class="w-full rounded-t bg-sky-600 group-hover:bg-sky-700 dark:bg-sky-400 dark:group-hover:bg-sky-300"
                        style="height: {{ $day['revenue'] > 0 ? max(2, round($day['revenue'] / $maxDay * 100)) : 0 }}%"
                    ></div>
                    <div class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-1 hidden -translate-x-1/2 whitespace-nowrap rounded-md bg-zinc-900 px-2 py-1 text-xs text-white shadow group-hover:block dark:bg-zinc-100 dark:text-zinc-900">
                        {{ $day['date']->format('D j M') }}: ₦{{ number_format($day['revenue']) }} · {{ trans_choice(':count order|:count orders', $day['orders']) }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-1 flex justify-between text-xs text-zinc-500 dark:text-zinc-400">
            <span>{{ $this->dailySales->first()['date']->format('j M') }}</span>
            <span>{{ __('Today') }}</span>
        </div>
    </div>

    {{-- Orders by status --}}
    <div class="{{ $panel }}">
        <flux:heading>{{ __('Orders by status') }}</flux:heading>
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach (\App\Enums\OrderStatus::cases() as $status)
                <a href="{{ route('admin.orders.index', ['status' => $status->value]) }}" wire:navigate>
                    <flux:badge size="sm">{{ __($status->label()) }} · {{ $this->ordersByStatus[$status->value] }}</flux:badge>
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        {{-- Recent orders --}}
        <div class="{{ $panel }} space-y-3">
            <div class="flex items-center justify-between">
                <flux:heading>{{ __('Recent orders') }}</flux:heading>
                <flux:link :href="route('admin.orders.index')" wire:navigate class="text-sm">{{ __('View all') }}</flux:link>
            </div>

            <flux:table>
                <flux:table.rows>
                    @forelse ($this->recentOrders as $order)
                        <flux:table.row :key="$order->id">
                            <flux:table.cell>
                                <flux:link :href="route('admin.orders.show', $order)" wire:navigate>{{ $order->reference }}</flux:link>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $order->customer_name }}</div>
                            </flux:table.cell>
                            <flux:table.cell class="tabular-nums">₦{{ number_format($order->subtotal) }}</flux:table.cell>
                            <flux:table.cell><x-admin.payment-badge :status="$order->payment_status" /></flux:table.cell>
                            <flux:table.cell class="text-xs">{{ $order->created_at?->diffForHumans() }}</flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell>{{ __('No orders yet.') }}</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="space-y-6">
            {{-- Best sellers --}}
            <div class="{{ $panel }} space-y-3">
                <flux:heading>{{ __('Best sellers, last 30 days') }}</flux:heading>

                @forelse ($this->bestSellers as $item)
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <span class="truncate text-zinc-900 dark:text-white">{{ $item->product_name }}</span>
                        <span class="shrink-0 tabular-nums text-zinc-500 dark:text-zinc-400">{{ trans_choice(':count sold|:count sold', (int) $item->units) }} · ₦{{ number_format((int) $item->revenue) }}</span>
                    </div>
                @empty
                    <flux:text>{{ __('Nothing sold in the last 30 days yet.') }}</flux:text>
                @endforelse
            </div>

            {{-- Stock alerts --}}
            <div class="{{ $panel }} space-y-3">
                <div class="flex items-center justify-between">
                    <flux:heading>{{ __('Low or out of stock') }}</flux:heading>
                    <flux:link :href="route('admin.products.index')" wire:navigate class="text-sm">{{ __('Products') }}</flux:link>
                </div>

                @forelse ($this->lowStock as $product)
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <flux:link :href="route('admin.products.edit', $product)" wire:navigate class="truncate">{{ $product->name }}</flux:link>
                        @if ($product->stock === 0)
                            <flux:badge color="red" size="sm" icon="x-circle">{{ __('Sold out') }}</flux:badge>
                        @else
                            <flux:badge color="amber" size="sm" icon="exclamation-triangle">{{ __(':count left', ['count' => $product->stock]) }}</flux:badge>
                        @endif
                    </div>
                @empty
                    <flux:text>{{ __('All counted products have more than :count in stock.', ['count' => \App\Livewire\Admin\Dashboard::LOW_STOCK]) }}</flux:text>
                @endforelse
            </div>
        </div>
    </div>
</section>
