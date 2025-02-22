<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory()->create()->id,
            'title' => $this->faker->sentence(),
            'description' => $this->faker->sentence(),
            'start_time' => $startTime = $this->faker->dateTimeBetween('-1 year', '+1 year'),
            'end_time' => Carbon::parse($startTime)->addHours(2),
            'status' => rand(1, 3),
        ];
    }
}
