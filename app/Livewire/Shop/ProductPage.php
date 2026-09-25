<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts::marketing')]
class ProductPage extends Component
{
    #[Locked]
    public Product $product;

    public int $quantity = 1;

    public bool $added = false;

    public function mount(Product $product): void
    {
        abort_unless($product->is_active, 404);

        $this->product = $product;
    }

    public function increment(): void
    {
        $this->quantity = min($this->quantity + 1, Cart::MAX_QUANTITY);
    }

    public function decrement(): void
    {
        $this->quantity = max($this->quantity - 1, 1);
    }

    public function addToCart(Cart $cart): void
    {
        $this->validate(['quantity' => ['required', 'integer', 'min:1', 'max:'.Cart::MAX_QUANTITY]]);

        $this->product->refresh();

        if (! $this->product->isPurchasable()) {
            $this->addError('quantity', 'This product can no longer be ordered online. Please enquire on WhatsApp.');

            return;
        }

        $cart->add($this->product, $this->quantity);

        $this->added = true;
        $this->quantity = 1;

        $this->dispatch('cart-updated');
    }

    /**
     * Other products from the same category, for the "You may also like" row.
     *
     * @return Collection<int, Product>
     */
    #[Computed]
    public function related(): Collection
    {
        return Product::query()
            ->active()
            ->where('category', $this->product->category)
            ->whereKeyNot($this->product->id)
            ->ordered()
            ->limit(4)
            ->get();
    }

    public function render(): View
    {
        /** @var \Illuminate\View\View $view */
        $view = view('livewire.shop.product-page');

        return $view
            ->title($this->product->name)
            ->layoutData(['description' => $this->product->description]);
    }
}
