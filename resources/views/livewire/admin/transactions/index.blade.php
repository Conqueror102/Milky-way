@php($panel = 'rounded-xl border border-zinc-200 p-4 dark:border-zinc-700')

<section class="w-full space-y-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Transactions') }}</flux:heading>
        <flux:subheading>{{ __('Every payment attempt made in the shop, newest first.') }}</flux:subheading>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="{{ $panel }}">
            <flux:text>{{ __('Attempts') }}</flux:text>
            <div class="mt-1 text-xl font-semibold tabular-nums text-zinc-900 dark:text-white">{{ number_format($this->summary['count']) }}</div>
        </div>
        <div class="{{ $panel }}">
            <flux:text>{{ __('Successful') }}</flux:text>
            <div class="mt-1 text-xl font-semibold tabular-nums text-zinc-900 dark:text-white">{{ number_format($this->summary['successful']) }}</div>
        </div>
        <div class="{{ $panel }}">
            <flux:text>{{ __('Money received') }}</flux:text>
            <div class="mt-1 text-xl font-semibold tabular-nums text-zinc-900 dark:text-white" data-test="received">₦{{ number_format($this->summary['received']) }}</div>
        </div>
        <div class="{{ $panel }}">
            <flux:text>{{ __('Failed') }}</flux:text>
            <div class="mt-1 text-xl font-semibold tabular-nums text-zinc-900 dark:text-white">{{ number_format($this->summary['failed']) }}</div>
        </div>
    </div>

    <div class="flex flex-wrap items-end gap-3">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search reference, order or customer')" class="min-w-64 flex-1" />

        <flux:select wire:model.live="status" class="max-w-44">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
            @foreach (\App\Enums\TransactionStatus::cases() as $option)
                <flux:select.option :value="$option->value">{{ __($option->label()) }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input type="date" wire:model.live="from" :label="__('From')" class="max-w-44" />
        <flux:input type="date" wire:model.live="to" :label="__('To')" class="max-w-44" />

        @if ($search !== '' || $status !== '' || $from !== '' || $to !== '')
            <flux:button variant="ghost" wire:click="clearFilters">{{ __('Clear') }}</flux:button>
        @endif
    </div>

    <flux:table :paginate="$this->payments">
        <flux:table.columns>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Reference') }}</flux:table.column>
            <flux:table.column>{{ __('Order') }}</flux:table.column>
            <flux:table.column>{{ __('Customer') }}</flux:table.column>
            <flux:table.column>{{ __('Amount') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Note') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->payments as $payment)
                <flux:table.row :key="$payment->id">
                    <flux:table.cell class="whitespace-nowrap">
                        {{ $payment->created_at?->format('j M Y, g:i a') }}
                        @if ($payment->paid_at)
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Paid :time', ['time' => $payment->paid_at->format('g:i a')]) }}</div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="font-mono text-xs">
                        {{ $payment->reference }}
                        <div class="font-sans text-zinc-500 dark:text-zinc-400">{{ ucfirst($payment->provider) }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:link :href="route('admin.orders.show', $payment->order)" wire:navigate>{{ $payment->order->reference }}</flux:link>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $payment->order->customer_name }}
                        @if ($payment->order->customer_email)
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $payment->order->customer_email }}</div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="tabular-nums">
                        ₦{{ number_format($payment->amount) }}
                        @if ($payment->amount_paid !== null && $payment->amount_paid !== $payment->amount)
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Received :amount', ['amount' => '₦'.number_format($payment->amount_paid)]) }}</div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell><x-admin.transaction-badge :status="$payment->status" /></flux:table.cell>
                    <flux:table.cell class="max-w-56 truncate text-sm">{{ $payment->message }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center">{{ __('No transactions match.') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
