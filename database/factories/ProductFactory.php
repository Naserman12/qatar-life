<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'pack' => $this->faker->numberBetween(1, 24),
            'size' => $this->faker->randomElement(['330ml', '500ml', '1L']),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'available' => true,
            'description' => $this->faker->sentence(),
            'image' => null,
            'category' => $this->faker->word(),
            'tax_included' => true,
        ];
    }
}
