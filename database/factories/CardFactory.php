<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => rand(1, User::count()),
            'card_holder_name' => $this->faker->name(),
            'card_brand'       => $this->faker->randomElement(['Visa', 'Mastercard', 'Amex']),
            'last4'            => $this->faker->numerify('####'),
            'expiry_month'     => $this->faker->numberBetween(1, 12),
            'expiry_year'      => $this->faker->numberBetween(now()->year, now()->year + 5),
            'is_primary'       => false,
            'tokenized_pan'    => $this->faker->uuid(),
        ];
    }
}
