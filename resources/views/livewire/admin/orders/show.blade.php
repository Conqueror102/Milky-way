<section class="w-full max-w-3xl space-y-6">
    <div>
        <flux:link :href="route('admin.orders.index')" wire:navigate class="text-sm">{{ __('Back to orders') }}</flux:link>
        <flux:heading size="xl" level="1" class="mt-2">{{ __('Order :reference', ['reference' => $order->reference]) }}</flux:heading>
        <flux:subheading class="flex flex-wrap items-center gap-2">
            {{ __('Placed :date', ['date' => $order->created_at?->format('j M Y, g:i a')]) }}
            <x-admin.payment-badge :status="$order->payment_status" />
            @if ($order->paid_at)
                <span>{{ __('Paid :date', ['date' => $order->paid_at->format('j M Y, g:i a')]) }}</span>
            @endif
            @if ($order->payment_reference)
                <span>{{ __('Ref :reference', ['reference' => $order->payment_reference]) }}</span>
            @endif
        </flux:subheading>
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div class="space-y-1">
            <flux:heading>{{ __('Customer') }}</flux:heading>
            <flux:text>{{ $order->customer_name }}</flux:text>
            @if ($order->customer_email)
                <flux:text>{{ $order->customer_email }}</flux:text>
            @endif
            <flux:text>{{ $order->customer_phone }}</flux:text>
        </div>

        <div class="space-y-1">
            <flux:heading>{{ __('Delivery') }}</flux:heading>
            <flux:text>{{ $order->delivery_area }}</flux:text>
            <flux:text class="whitespace-pre-line">{{ $order->delivery_address }}</flux:text>
        </div>
    </div>

    @if ($order->notes)
        <div class="space-y-1">
            <flux:heading>{{ __('Notes') }}</flux:heading>
            <flux:text class="whitespace-pre-line">{{ $order->notes }}</flux:text>
        </div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Item') }}</flux:table.column>
            <flux:table.column>{{ __('Price') }}</flux:table.column>
            <flux:table.column>{{ __('Qty') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Subtotal') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($order->items as $item)
                <flux:table.row :key="$item->id">
                    <flux:table.cell>{{ $item->product_name }}</flux:table.cell>
                    <flux:table.cell>₦{{ number_format($item->unit_price) }}</flux:table.cell>
                    <flux:table.cell>{{ $item->quantity }}</flux:table.cell>
                    <flux:table.cell align="end">₦{{ number_format($item->line_total) }}</flux:table.cell>
                </flux:table.row>
            @endforeach
            <flux:table.row>
                <flux:table.cell colspan="3" class="font-medium">{{ __('Total') }}</flux:table.cell>
                <flux:table.cell align="end" class="font-medium">₦{{ number_format($order->subtotal) }}</flux:table.cell>
            </flux:table.row>
        </flux:table.rows>
    </flux:table>

    <form wire:submit="updateStatus" class="flex items-end gap-4">
        <flux:select wire:model="status" :label="__('Status')" class="max-w-48">
            @foreach (\App\Enums\OrderStatus::cases() as $option)
                <flux:select.option :value="$option->value">{{ __($option->label()) }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:button type="submit" variant="primary">{{ __('Update status') }}</flux:button>
    </form>
</section>
