<?php

namespace App\Livewire\Shop;

use App\Support\Cart;
use App\Support\CartLine;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * @property-read Collection<int, CartLine> $lines
 * @property-read int $subtotal
 */
#[Layout('layouts::marketing', ['noindex' => true])]
#[Title('Your cart')]
class CartPage extends Component
{
    public function increment(Cart $cart, int $productId): void
    {
        $line = $this->lines->first(fn (CartLine $line) => $line->product->id === $productId);

        if ($line !== null) {
            $this->changeQuantity($cart, $productId, $line->quantity + 1);
        }
    }

    public function decrement(Cart $cart, int $productId): void
    {
        $line = $this->lines->first(fn (CartLine $line) => $line->product->id === $productId);

        if ($line !== null) {
            $this->changeQuantity($cart, $productId, $line->quantity - 1);
        }
    }

    public function remove(Cart $cart, int $productId): void
    {
        $this->changeQuantity($cart, $productId, 0);
    }

    /**
     * @return Collection<int, CartLine>
     */
    #[Computed]
    public function lines(): Collection
    {
        return app(Cart::class)->lines();
    }

    #[Computed]
    public function subtotal(): int
    {
        return app(Cart::class)->subtotal($this->lines);
    }

    private function changeQuantity(Cart $cart, int $productId, int $quantity): void
    {
        $cart->update($productId, $quantity);

        unset($this->lines, $this->subtotal);

        $this->dispatch('cart-updated');
    }
}
