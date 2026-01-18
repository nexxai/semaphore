<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DayTask>
 */
class DayTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'day_id' => \App\Models\Day::factory(),
            'task_id' => \App\Models\Task::factory(),
            'completed' => false,
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
