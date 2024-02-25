<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "stan" => Str::random(32),
            "fee" => 0,
            "tax" => 0,
            "account_number" => fake()->phoneNumber(),
            "account_issuer" => fake()->phoneNumber(),
            "type" => fake()->randomElement(['user', 'agent', 'lender']),
            "amount" => fake()->randomDigitNotZero(),
            "amount" => fake()->randomDigitNotZero(),
            "amount" => fake()->randomDigitNotZero(),
            "description" => fake()->randomDigitNotZero(),
            "user_id" => User::factory()
        ];
    }
}
