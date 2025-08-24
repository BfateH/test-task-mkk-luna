<?php

namespace Database\Factories;

use App\Models\Organization;
use Database\Seeders\OrganizationSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationPhone>
 */
class OrganizationPhoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone' => fake()->phoneNumber(),
            'organization_id' => Organization::query()->inRandomOrder()->first()->id,
        ];
    }
}
