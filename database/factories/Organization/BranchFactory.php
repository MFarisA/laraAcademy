<?php

namespace Database\Factories\Organization;

use App\Models\Organization\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Branch',
            'code' => strtoupper(fake()->bothify('???-##')),
            'city' => fake()->city(),
            'address' => fake()->address(),
        ];
    }
}
