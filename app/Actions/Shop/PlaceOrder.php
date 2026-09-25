<?php

namespace App\Actions\Shop;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Support\Cart;
use App\Support\CartLine;
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
        $lines = $this->cart->lines();

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty. Add a product before placing an order.',
            ]);
        }

        $order = DB::transaction(function () use ($details, $userId, $lines) {
            $order = Order::create([
                'reference' => $this->newReference(),
                'user_id' => $userId,
                'status' => OrderStatus::Pending,
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
