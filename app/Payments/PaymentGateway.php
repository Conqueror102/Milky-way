<?php

namespace App\Payments;

use App\Models\Order;

/**
 * The hook for online payment. A provider (e.g. Paystack or Flutterwave) implements
 * this, is bound in a service provider, and is switched on with SHOP_PAYMENT_GATEWAY.
 *
 * checkoutUrl() starts a payment for the order's subtotal and returns the provider's
 * hosted payment page. The provider's callback or webhook should then verify the
 * payment and set the order's payment_status to paid, with payment_provider,
 * payment_reference and paid_at.
 */
interface PaymentGateway
{
    public function checkoutUrl(Order $order): string;
}
