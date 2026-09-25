<?php

namespace App\Livewire\Admin\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Show extends Component
{
    public Order $order;

    public string $status = '';

    public function mount(Order $order): void
    {
        $this->order = $order->load('items');
        $this->status = $order->status->value;
    }

    public function updateStatus(): void
    {
        $this->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
        ]);

        $this->order->update(['status' => OrderStatus::from($this->status)]);

        Flux::toast(variant: 'success', text: __('Order status updated.'));
    }

    public function render(): View
    {
        return view('livewire.admin.orders.show')
            ->title(__('Order :reference', ['reference' => $this->order->reference]));
    }
}
