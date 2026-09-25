<section class="w-full space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Orders') }}</flux:heading>
            <flux:subheading>{{ __('Orders placed in the shop, newest first.') }}</flux:subheading>
        </div>

        <flux:select wire:model.live="status" class="max-w-48">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
            @foreach (\App\Enums\OrderStatus::cases() as $option)
                <flux:select.option :value="$option->value">{{ __($option->label()) }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <flux:table :paginate="$this->orders">
        <flux:table.columns>
            <flux:table.column>{{ __('Order') }}</flux:table.column>
            <flux:table.column>{{ __('Customer') }}</flux:table.column>
            <flux:table.column>{{ __('Items') }}</flux:table.column>
            <flux:table.column>{{ __('Total') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Placed') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->orders as $order)
                <flux:table.row :key="$order->id">
                    <flux:table.cell>
                        <flux:link :href="route('admin.orders.show', $order)" wire:navigate>{{ $order->reference }}</flux:link>
                    </flux:table.cell>
                    <flux:table.cell>{{ $order->customer_name }}</flux:table.cell>
                    <flux:table.cell>{{ $order->items_count }}</flux:table.cell>
                    <flux:table.cell>₦{{ number_format($order->subtotal) }}</flux:table.cell>
                    <flux:table.cell><flux:badge size="sm">{{ __($order->status->label()) }}</flux:badge></flux:table.cell>
                    <flux:table.cell>{{ $order->created_at?->diffForHumans() }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center">{{ __('No orders yet.') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
