<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
use App\Models\Product;
class ProductFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
     protected $model = Product::class;
    public function definition(): array
    {
        
        return [
                 'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 5, 500),
            'pack' => $this->faker->numberBetween(6, 24),
            'size' => $this->faker->randomElement(['صغير', 'وسط', 'كبير']),
            'available' => true,
        ];
    }
}
