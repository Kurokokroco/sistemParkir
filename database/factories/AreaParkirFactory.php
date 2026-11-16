<?php

namespace Database\Factories;

use App\Models\AreaParkir;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AreaParkir>
 */
class AreaParkirFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_area' => fake()->unique()->words(2, true),
            'jenis_kendaraan' => fake()->randomElement(['motor', 'mobil']),
            'kapasitas' => fake()->numberBetween(1, 100),
        ];
    }
}
