<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * The shopper's cart, kept in their session as product id => quantity. Prices are never
 * stored here: they are read fresh from the products table every time, so a price change
 * reaches carts that are already open.
 */
class Cart
{
    public const SESSION_KEY = 'cart';

    public const MAX_QUANTITY = 99;

    public function __construct(private Session $session) {}

    /**
     * Add units of a product, never beyond what's in stock. Returns how many were added.
     */
    public function add(Product $product, int $quantity = 1): int
    {
        if (! $product->isPurchasable() || $quantity < 1) {
            return 0;
        }

        $current = $this->quantityOf($product->id);
        $new = min($current + $quantity, $product->maxOrderQuantity());

        $this->put($product->id, $new);

        return max($new - $current, 0);
    }

    /**
     * Set a line's quantity outright, capped at what's in stock; zero or less removes it.
     */
    public function update(int $productId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->remove($productId);

            return;
        }

        $product = Product::find($productId);

        if ($product !== null && array_key_exists($productId, $this->items())) {
            $this->put($productId, min($quantity, $product->maxOrderQuantity()));
        }
    }

    public function quantityOf(int $productId): int
    {
        return $this->items()[$productId] ?? 0;
    }

    public function remove(int $productId): void
    {
        $items = $this->items();

        unset($items[$productId]);

        $this->session->put(self::SESSION_KEY, $items);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    /**
     * The cart's lines, in the order they were added. Products that have since been
     * hidden, deleted, sold out or lost their price drop out here and from the session.
     *
     * @return Collection<int, CartLine>
     */
    public function lines(): Collection
    {
        $items = $this->items();

        if ($items === []) {
            return collect();
        }

        $products = Product::query()->whereKey(array_keys($items))->get()->keyBy('id');

        // Stock can drop while a cart is open, so each line is capped at what's left.
        $lines = collect($items)
            ->map(fn (int $quantity, int $productId) => ($product = $products->get($productId)) instanceof Product && $product->isPurchasable()
                ? new CartLine($product, min($quantity, $product->maxOrderQuantity()))
                : null)
            ->filter()
            ->values();

        $kept = $lines->mapWithKeys(fn (CartLine $line) => [$line->product->id => $line->quantity])->all();

        if ($kept !== $items) {
            $this->session->put(self::SESSION_KEY, $kept);
        }

        return $lines;
    }

    /**
     * @param  Collection<int, CartLine>|null  $lines
     */
    public function subtotal(?Collection $lines = null): int
    {
        return (int) ($lines ?? $this->lines())->sum(fn (CartLine $line) => $line->total());
    }

    /**
     * How many units are in the cart, for the header badge.
     */
    public function count(): int
    {
        return array_sum($this->items());
    }

    public function isEmpty(): bool
    {
        return $this->items() === [];
    }

    /**
     * @return array<int, int>
     */
    private function items(): array
    {
        $items = $this->session->get(self::SESSION_KEY, []);

        return is_array($items) ? $items : [];
    }

    private function put(int $productId, int $quantity): void
    {
        $items = $this->items();

        $items[$productId] = min($quantity, self::MAX_QUANTITY);

        $this->session->put(self::SESSION_KEY, $items);
    }
}
