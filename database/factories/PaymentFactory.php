<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rental;

class PaymentFactory extends Factory {
    public function definition(): array {
        return [
            'rental_id' => Rental::factory(),
            'amount' => $this->faker->randomFloat(2, 50, 1000),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'cash']),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
        ];
    }
}
