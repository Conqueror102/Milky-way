<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * The round add-to-cart button on a product card: adds one at a time, within stock.
 */
class CardAddToCart extends Component
{
    public Product $product;

    public bool $added = false;

    public ?string $problem = null;

    public function add(Cart $cart): void
    {
        $this->product->refresh();
        $this->added = false;
        $this->problem = null;

        if (! $this->product->isPurchasable()) {
            $this->problem = $this->product->isSoldOut() ? 'Sold out' : 'Not available online';

            return;
        }

        if ($cart->quantityOf($this->product->id) >= $this->product->maxOrderQuantity()) {
            $this->problem = 'All in stock are in your cart';

            return;
        }

        $cart->add($this->product);

        $this->added = true;

        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        return view('livewire.shop.card-add-to-cart');
    }
}
