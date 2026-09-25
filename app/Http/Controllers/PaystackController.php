<?php

namespace App\Http\Controllers;

use App\Actions\Shop\RecordPayment;
use App\Models\Order;
use App\Payments\PaystackGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaystackController extends Controller
{
    public function __construct(
        private PaystackGateway $paystack,
        private RecordPayment $recordPayment,
    ) {}

    /**
     * Where Paystack sends the shopper back. The query string is never trusted: the
     * transaction is re-read from Paystack before the order is touched.
     */
    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->string('reference', $request->string('trxref')->toString())->toString();

        $order = Order::where('payment_reference', $reference)->first();

        abort_if($reference === '' || $order === null, 404);

        $result = $this->paystack->verify($reference);

        if ($result !== null && $result['status'] === 'success') {
            $this->recordPayment->succeeded($order, $reference, $result['amount']);
        } elseif ($result !== null && $result['status'] === 'failed') {
            $this->recordPayment->failed($order, $reference);
        }

        $order->refresh();

        return redirect()
            ->route('orders.show', $order)
            ->with('payment_notice', match (true) {
                $order->isPaid() => null,
                $result === null => 'We could not confirm your payment yet. If you were charged, it will show here shortly.',
                default => 'Your payment did not go through. You can try again below.',
            });
    }

    /**
     * Paystack's server-to-server notice, which arrives even if the shopper closes the
     * tab before returning. Only signed requests are acted on.
     */
    public function webhook(Request $request): Response
    {
        abort_unless($this->paystack->hasValidSignature($request->getContent(), $request->headers->get('x-paystack-signature')), 401);

        if ($request->input('event') === 'charge.success') {
            $reference = $request->string('data.reference')->toString();
            $orderReference = $request->input('data.metadata.order_reference');

            $order = Order::where('payment_reference', $reference)->first()
                ?? (is_string($orderReference) ? Order::where('reference', $orderReference)->first() : null);

            if ($order !== null) {
                $this->recordPayment->succeeded($order, $reference, $request->integer('data.amount'));
            }
        }

        return response()->noContent(200);
    }
}
