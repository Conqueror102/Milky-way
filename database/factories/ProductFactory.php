<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(implode(' ', fake()->unique()->words(3)));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'category' => fake()->randomElement(['Skincare', 'Health & Beauty', 'Spa & Massage']),
            'type' => fake()->randomElement(['Serum', 'Body Oil', 'Tea']),
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(20, 500) * 100,
            'image_url' => null,
            'image_path' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    /**
     * A product listed without a price, so it can only be enquired about.
     */
    public function priceOnRequest(): static
    {
        return $this->state(fn (array $attributes) => ['price' => null]);
    }

    /**
     * A product hidden from the shop.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
