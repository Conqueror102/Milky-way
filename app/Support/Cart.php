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

    public function add(Product $product, int $quantity = 1): void
    {
        if (! $product->isPurchasable() || $quantity < 1) {
            return;
        }

        $items = $this->items();

        $this->put($product->id, ($items[$product->id] ?? 0) + $quantity);
    }

    /**
     * Set a line's quantity outright; zero or less removes it.
     */
    public function update(int $productId, int $quantity): void
    {
        if ($quantity < 1) {
            $this->remove($productId);

            return;
        }

        if (array_key_exists($productId, $this->items())) {
            $this->put($productId, $quantity);
        }
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
     * hidden, deleted or lost their price drop out here and from the session.
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

        $lines = collect($items)
            ->map(fn (int $quantity, int $productId) => ($product = $products->get($productId)) instanceof Product && $product->isPurchasable()
                ? new CartLine($product, $quantity)
                : null)
            ->filter()
            ->values();

        if ($lines->count() !== count($items)) {
            $this->session->put(
                self::SESSION_KEY,
                $lines->mapWithKeys(fn (CartLine $line) => [$line->product->id => $line->quantity])->all(),
            );
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
