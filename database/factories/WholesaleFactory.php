<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wholesale>
 */
class WholesaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id'  => mt_rand(1,5),
            'price' => $this->faker->randomNumber(5),
            'quantity' => mt_rand(1,10),
        ];
    }
}
