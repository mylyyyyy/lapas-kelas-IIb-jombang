<?php

namespace Database\Factories;

use App\Models\Wbp;
use Illuminate\Database\Eloquent\Factories\Factory;

class WbpFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wbp::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama' => $this->faker->name(),
            'no_registrasi' => 'C' . $this->faker->unique()->numerify('####'),
            'blok' => $this->faker->randomElement(['A', 'B', 'C']),
            'kamar' => $this->faker->numberBetween(1, 10),
        ];
    }
}
