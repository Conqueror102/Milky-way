<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'provider' => 'paystack',
            'reference' => 'MW-'.Str::upper(Str::random(12)),
            'status' => TransactionStatus::Pending,
            'amount' => 10000,
        ];
    }

    public function successful(): static
    {
        return $this->state([
            'status' => TransactionStatus::Successful,
            'paid_at' => now(),
        ])->afterMaking(function (Payment $payment) {
            $payment->amount_paid ??= $payment->amount;
        });
    }
}
