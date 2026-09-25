<?php

namespace App\Livewire\Admin\Orders;

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
        $this->status = $order->status;
    }

    public function updateStatus(): void
    {
        $this->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
        ]);

        $this->order->update(['status' => $this->status]);

        Flux::toast(variant: 'success', text: __('Order status updated.'));
    }

    public function render(): View
    {
        return view('livewire.admin.orders.show')
            ->title(__('Order #:number', ['number' => $this->order->id]));
    }
}
