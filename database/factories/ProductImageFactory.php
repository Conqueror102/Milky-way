<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'url' => 'https://res.cloudinary.com/demo/image/upload/'.fake()->uuid().'.jpg',
            'public_id' => null,
            'alt' => null,
            'sort_order' => 0,
        ];
    }
}
