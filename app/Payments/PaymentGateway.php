<?php

namespace App\Payments;

use App\Models\Order;

/**
 * The online payment provider. Bound in AppServiceProvider from config
 * milkyway.shop.payment_gateway; Paystack is the one in use.
 *
 * checkoutUrl() starts a payment for the order's subtotal and returns the provider's
 * hosted payment page. The provider's callback and webhook then confirm the payment,
 * which App\Actions\Shop\RecordPayment writes to the order.
 */
interface PaymentGateway
{
    public function checkoutUrl(Order $order): string;
}
