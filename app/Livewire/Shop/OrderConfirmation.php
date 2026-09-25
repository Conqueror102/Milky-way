<?php

namespace App\Livewire\Shop;

use App\Models\Order;
use App\Payments\PaymentGateway;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Where checkout lands: the placed order, and the step that takes payment for it.
 */
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
     * Whether an online payment provider is switched on.
     */
    public function paymentsEnabled(): bool
    {
        return filled(config('milkyway.shop.payment_gateway'));
    }

    public function pay(): void
    {
        if (! $this->paymentsEnabled() || $this->order->isPaid()) {
            return;
        }

        $this->redirect(app(PaymentGateway::class)->checkoutUrl($this->order));
    }

    public function render(): View
    {
        /** @var \Illuminate\View\View $view */
        $view = view('livewire.shop.order-confirmation');

        return $view->title('Order '.$this->order->reference);
    }
}
