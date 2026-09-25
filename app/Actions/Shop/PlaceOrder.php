<?php

namespace App\Actions\Shop;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Support\Cart;
use App\Support\CartLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PlaceOrder
{
    public function __construct(private Cart $cart) {}

    /**
     * Turn the current cart into an order and empty the cart.
     *
     * @param  array{customer_name: string, customer_phone: string, customer_email?: string|null, delivery_area: string, delivery_address: string, notes?: string|null}  $details
     *
     * @throws ValidationException when the cart has nothing left to order
     */
    public function handle(array $details, ?int $userId = null): Order
    {
        $requested = $this->cart->count();
        $lines = $this->cart->lines();

        // lines() trims anything that sold out or went off sale since it was carted. Stop
        // here rather than quietly charging for a different order than the shopper saw.
        if ($lines->isNotEmpty() && $lines->sum(fn (CartLine $line) => $line->quantity) !== $requested) {
            throw ValidationException::withMessages([
                'cart' => 'Some items in your cart have changed because of stock. Please check your cart and try again.',
            ]);
        }

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty. Add a product before placing an order.',
            ]);
        }

        $order = DB::transaction(function () use ($details, $userId, $lines) {
            $this->reserveStock($lines);

            $order = Order::create([
                'reference' => $this->newReference(),
                'user_id' => $userId,
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Unpaid,
                'customer_name' => $details['customer_name'],
                'customer_phone' => $details['customer_phone'],
                'customer_email' => $details['customer_email'] ?? null,
                'delivery_area' => $details['delivery_area'],
                'delivery_address' => $details['delivery_address'],
                'notes' => $details['notes'] ?? null,
                'subtotal' => $this->cart->subtotal($lines),
            ]);

            $order->items()->createMany($lines->map(fn (CartLine $line) => [
                'product_id' => $line->product->id,
                'product_name' => $line->product->name,
                'unit_price' => $line->unitPrice(),
                'quantity' => $line->quantity,
                'line_total' => $line->total(),
            ])->all());

            return $order;
        });

        $this->cart->clear();

        return $order;
    }

    /**
     * Take the ordered units out of stock, re-reading each tracked product under a lock
     * so two shoppers can't both buy the last one.
     *
     * @param  Collection<int, CartLine>  $lines
     *
     * @throws ValidationException when a product no longer has enough stock
     */
    private function reserveStock(Collection $lines): void
    {
        foreach ($lines as $line) {
            $product = Product::query()->lockForUpdate()->find($line->product->id);

            if ($product === null || $product->stock === null) {
                continue;
            }

            if ($product->stock < $line->quantity) {
                throw ValidationException::withMessages([
                    'cart' => $product->stock > 0
                        ? "Only {$product->stock} of {$product->name} left. Please update your cart."
                        : "{$product->name} has just sold out. Please remove it from your cart.",
                ]);
            }

            $product->decrement('stock', $line->quantity);
        }
    }

    /**
     * A short reference that is easy to read out over the phone or paste into WhatsApp.
     */
    private function newReference(): string
    {
        do {
            $reference = 'MW-'.Str::upper(Str::random(6));
        } while (Order::where('reference', $reference)->exists());

        return $reference;
    }
}
