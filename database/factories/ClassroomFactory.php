<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->sentence(3),
            'category' => $this->faker->randomElement(['Teknologi', 'Kreativitas', 'Produktivitas', 'Pengembangan Diri']),
            'description' => $this->faker->paragraph(),
            'status' => 'draft',
        ];
    }
}
