<?php

namespace App\Livewire\Shop;

use App\Support\Cart;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The cart badge in the site header. Anything that changes the cart dispatches
 * "cart-updated", and this re-renders with the new count.
 */
class CartCount extends Component
{
    #[On('cart-updated')]
    public function refreshCount(): void
    {
        // Re-rendering is all that's needed; the count is read fresh in render().
    }

    public function render(Cart $cart): View
    {
        return view('livewire.shop.cart-count', ['count' => $cart->count()]);
    }
}
