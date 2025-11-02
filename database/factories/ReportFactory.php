<?php

namespace Database\Factories;

use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * Define the model's default state
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-30 days', '-7 days');
        $end = $this->faker->dateTimeBetween('-6 days', 'now');

        return [
            'period_start' => $start,
            'period_end' => $end,
            'payload' => [
                'total_tasks' => $this->faker->numberBetween(10, 50),
                'done_tasks' => $this->faker->numberBetween(5, 30),
                'blocked_tasks' => $this->faker->numberBetween(0, 5),
            ],
            'path' => 'reports/'.$this->faker->uuid.'.pdf',
        ];
    }
}
