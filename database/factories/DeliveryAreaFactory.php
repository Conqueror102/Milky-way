<?php

namespace Database\Factories;

use App\Models\DeliveryArea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryArea>
 */
class DeliveryAreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city(),
            'fee' => null,
            'is_featured' => false,
            'sort_order' => 100,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
