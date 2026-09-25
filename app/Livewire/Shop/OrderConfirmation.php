<?php

namespace App\Livewire\Shop;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts::marketing', ['noindex' => true])]
class OrderConfirmation extends Component
{
    #[Locked]
    public Order $order;

    public function mount(Order $order): void
    {
        /** @var array<int, string> $placed */
        $placed = session(Checkout::PLACED_ORDERS_SESSION_KEY, []);

        $ownsOrder = in_array($order->reference, $placed, true)
            || ($order->user_id !== null && $order->user_id === Auth::id());

        abort_unless($ownsOrder, 404);

        $this->order = $order->load('items');
    }

    /**
     * The WhatsApp message that hands this order over to the store to confirm.
     */
    public function whatsappMessage(): string
    {
        $lines = $this->order->items
            ->map(fn (OrderItem $item) => "- {$item->quantity} x {$item->product_name} ({$item->formattedLineTotal()})")
            ->implode("\n");

        return "Hello Milkyway Cosmetics Stores, I just placed order {$this->order->reference} on your website.\n\n"
            .$lines."\n\n"
            ."Subtotal: {$this->order->formattedSubtotal()}\n"
            ."Deliver to: {$this->order->delivery_area}\n\n"
            .'Please confirm availability and delivery.';
    }

    public function render(): View
    {
        /** @var \Illuminate\View\View $view */
        $view = view('livewire.shop.order-confirmation');

        return $view->title('Order '.$this->order->reference);
    }
}
