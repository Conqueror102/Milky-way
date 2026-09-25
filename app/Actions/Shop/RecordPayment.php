<?php

namespace App\Actions\Shop;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Writes a provider's verdict on a payment to its order. Safe to call more than once for
 * the same payment: the callback and the webhook usually both report it.
 */
class RecordPayment
{
    /**
     * @param  int  $amountPaid  in kobo, as the provider reports it
     */
    public function succeeded(Order $order, string $reference, int $amountPaid): void
    {
        DB::transaction(function () use ($order, $reference, $amountPaid) {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->isPaid()) {
                return;
            }

            if ($amountPaid < $order->subtotal * 100) {
                Log::warning('Payment for less than the order total', [
                    'order' => $order->reference,
                    'reference' => $reference,
                    'paid_kobo' => $amountPaid,
                    'due_kobo' => $order->subtotal * 100,
                ]);

                $order->update(['payment_status' => PaymentStatus::Failed, 'payment_reference' => $reference]);

                return;
            }

            $order->update([
                'status' => OrderStatus::Confirmed,
                'payment_status' => PaymentStatus::Paid,
                'payment_reference' => $reference,
                'paid_at' => now(),
            ]);
        });
    }

    public function failed(Order $order, string $reference): void
    {
        if ($order->isPaid()) {
            return;
        }

        $order->update(['payment_status' => PaymentStatus::Failed, 'payment_reference' => $reference]);
    }
}
