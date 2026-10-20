<?php

namespace App\Actions\Shop;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CancelOrder
{
    public function handle(Order $order, bool $expiredOnly = false): bool
    {
        return DB::transaction(function () use ($order, $expiredOnly): bool {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->status === OrderStatus::Cancelled) {
                return false;
            }

            if ($expiredOnly && ($order->isPaid() || $order->status !== OrderStatus::Pending || $order->created_at->gt(now()->subDay()))) {
                return false;
            }

            foreach ($order->items()->orderBy('product_id')->get() as $item) {
                Product::query()->whereKey($item->product_id)->whereNotNull('stock')->increment('stock', $item->quantity);
            }

            $order->update(['status' => OrderStatus::Cancelled]);

            return true;
        });
    }
}
