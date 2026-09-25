<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => 'MW-'.Str::upper(Str::random(8)),
            'status' => OrderStatus::Pending,
            'customer_name' => fake()->name(),
            'customer_phone' => '+234 800 000 0000',
            'customer_email' => fake()->safeEmail(),
            'delivery_area' => 'Lagos',
            'delivery_address' => fake()->address(),
            'notes' => null,
            'subtotal' => 0,
        ];
    }
}
