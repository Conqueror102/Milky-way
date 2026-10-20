<?php

namespace App\Livewire\Admin\Orders;

use App\Actions\Shop\CancelOrder;
use App\Actions\Shop\RecordPayment;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Payments\PaystackGateway;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Show extends Component
{
    public Order $order;

    public string $status = '';

    public ?string $paymentNotice = null;

    public function mount(Order $order): void
    {
        $this->order = $order->load('items', 'payments');
        $this->status = $order->status->value;
    }

    public function updateStatus(CancelOrder $cancelOrder): void
    {
        Gate::authorize('admin');
        $this->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
        ]);

        DB::transaction(function () use ($cancelOrder): void {
            $order = Order::query()->lockForUpdate()->findOrFail($this->order->id);
            $status = OrderStatus::from($this->status);

            if ($order->status === OrderStatus::Cancelled && $status !== OrderStatus::Cancelled) {
                throw ValidationException::withMessages(['status' => 'Cancelled orders cannot be reopened. Place a new order instead.']);
            }

            if ($status === OrderStatus::Cancelled) {
                $cancelOrder->handle($order);
            } else {
                $order->update(['status' => $status]);
            }
        });

        $this->order->refresh()->load('items', 'payments');

        Flux::toast(variant: 'success', text: __('Order status updated.'));
    }

    public function checkPayment(int $paymentId, PaystackGateway $paystack, RecordPayment $recordPayment): void
    {
        Gate::authorize('admin');

        $payment = $this->order->payments()->where('provider', 'paystack')->find($paymentId);

        abort_if($payment === null, 404);
        $result = $paystack->verify($payment->reference);

        if ($result === null) {
            $this->paymentNotice = 'Paystack is unavailable. Nothing was changed; please check again shortly.';

            return;
        }

        match ($result['status']) {
            'success' => $recordPayment->succeeded($this->order, $payment->reference, $result['amount']),
            'failed' => $recordPayment->failed($this->order, $payment->reference, $result['message']),
            'abandoned' => $recordPayment->abandoned($this->order, $payment->reference),
            default => null,
        };

        $this->order->refresh()->load('items', 'payments');
        $this->status = $this->order->status->value;
        $this->paymentNotice = match ($result['status']) {
            'success' => $this->order->isPaid() ? 'Payment confirmed. Review the payment attempts below for any refund needed.' : 'Payment amount does not cover the order. Review the payment attempt below.',
            'failed' => 'Paystack reports this attempt failed.',
            'abandoned' => 'Paystack reports this attempt was abandoned.',
            default => 'Payment is still awaiting confirmation. Please check again shortly.',
        };
    }

    public function render(): View
    {
        return view('livewire.admin.orders.show')
            ->title(__('Order :reference', ['reference' => $this->order->reference]));
    }
}
