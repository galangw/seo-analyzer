<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(rand(6, 10)),
            'meta_description' => $this->faker->paragraph(1),
            'content' => $this->faker->paragraphs(rand(5, 10), true),
            'target_keyword' => $this->faker->words(rand(1, 3), true),
        ];
    }
} 