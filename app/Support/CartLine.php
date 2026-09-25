<?php

namespace App\Support;

use App\Models\Product;

final readonly class CartLine
{
    public function __construct(
        public Product $product,
        public int $quantity,
    ) {}

    public function unitPrice(): int
    {
        return (int) $this->product->price;
    }

    public function total(): int
    {
        return $this->unitPrice() * $this->quantity;
    }
}
