<?php

namespace App\Actions\Shop;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Writes a provider's verdict on a payment to its order and to that attempt's payment
 * row. Safe to call more than once for the same payment: the callback and the webhook
 * usually both report it.
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
            $payment = $this->attempt($order, $reference);

            if ($payment->status === TransactionStatus::Successful) {
                return;
            }

            if ($amountPaid < $order->subtotal * 100) {
                Log::warning('Payment for less than the order total', [
                    'order' => $order->reference,
                    'reference' => $reference,
                    'paid_kobo' => $amountPaid,
                    'due_kobo' => $order->subtotal * 100,
                ]);

                $payment->update([
                    'status' => TransactionStatus::Failed,
                    'amount_paid' => intdiv($amountPaid, 100),
                    'message' => 'Paid less than the order total',
                ]);

                if (! $order->isPaid()) {
                    $order->update(['payment_status' => PaymentStatus::Failed, 'payment_reference' => $reference]);
                }

                return;
            }

            $payment->update([
                'status' => TransactionStatus::Successful,
                'amount_paid' => intdiv($amountPaid, 100),
                'message' => null,
                'paid_at' => now(),
            ]);

            if ($order->isPaid()) {
                // A second successful attempt on an already paid order: kept on the
                // payment row so the admin can spot it and refund it.
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

    public function failed(Order $order, string $reference, ?string $message = null): void
    {
        $payment = $this->attempt($order, $reference);

        if ($payment->status !== TransactionStatus::Successful) {
            $payment->update(['status' => TransactionStatus::Failed, 'message' => $message]);
        }

        if ($order->isPaid()) {
            return;
        }

        $order->update(['payment_status' => PaymentStatus::Failed, 'payment_reference' => $reference]);
    }

    /**
     * The shopper left the provider's page without paying. The order is untouched, so
     * they can still try again.
     */
    public function abandoned(Order $order, string $reference): void
    {
        $payment = $this->attempt($order, $reference);

        if ($payment->status === TransactionStatus::Pending) {
            $payment->update(['status' => TransactionStatus::Abandoned]);
        }
    }

    /**
     * The payment row for this attempt. Made here if it is missing, e.g. for a payment
     * started before payment rows were kept.
     */
    private function attempt(Order $order, string $reference): Payment
    {
        return Payment::query()->firstOrCreate(['reference' => $reference], [
            'order_id' => $order->id,
            'provider' => $order->payment_provider ?? 'paystack',
            'status' => TransactionStatus::Pending,
            'amount' => $order->subtotal,
        ]);
    }
}
