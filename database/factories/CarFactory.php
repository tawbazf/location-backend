<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\car>
 */
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory {
    public function definition(): array {
        return [
            'brand' => $this->faker->company(),
            'model' => $this->faker->word(),
            'year' => $this->faker->year(),
            'price_per_day' => $this->faker->randomFloat(2, 20, 200), // Random price
            'is_available' => $this->faker->boolean(80), // 80% chance it's available
        ];
    }
}
