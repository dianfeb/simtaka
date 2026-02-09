<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
        'nis' => '2024' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
        'name' => fake()->name(),
        'gender' => fake()->randomElement(['L', 'P']),
        'birth_date' => fake()->dateTimeBetween('-6 years', '-4 years'),
        'birth_place' => fake()->city(),
        'address' => fake()->address(),
        'parent_id' => User::where('role', 'orang_tua')->inRandomOrder()->first()->id,
        'father_name' => fake()->name('male'),
        'mother_name' => fake()->name('female'),
        'status' => 'active',
        'registration_date' => fake()->dateTimeBetween('-1 year', 'now'),
    ];
    }
}
