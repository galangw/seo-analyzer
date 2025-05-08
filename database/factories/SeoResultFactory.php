<?php

namespace Database\Factories;

use App\Models\Content;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SeoResult>
 */
class SeoResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pageTitleScore = $this->faker->numberBetween(30, 100);
        $metaDescriptionScore = $this->faker->numberBetween(30, 100);
        $contentScore = $this->faker->numberBetween(30, 100);
        $overallScore = ($pageTitleScore + $metaDescriptionScore + $contentScore) / 3;

        $detailScore = [
            'page_title' => [
                'score' => $pageTitleScore / 100,
                'details' => [
                    'has_keyword' => $this->faker->boolean(80),
                    'length_appropriate' => $this->faker->boolean(70),
                    'suggestions' => $this->faker->randomElements([
                        'Add keyword at the beginning',
                        'Make title more compelling',
                        'Keep title between 50-60 characters'
                    ], $this->faker->numberBetween(0, 2))
                ]
            ],
            'meta_description' => [
                'score' => $metaDescriptionScore / 100,
                'details' => [
                    'has_keyword' => $this->faker->boolean(75),
                    'length_appropriate' => $this->faker->boolean(80),
                    'suggestions' => $this->faker->randomElements([
                        'Include main keyword in description',
                        'Add call to action',
                        'Keep description between 150-160 characters'
                    ], $this->faker->numberBetween(0, 2))
                ]
            ],
            'content' => [
                'score' => $contentScore / 100,
                'details' => [
                    'keyword_density' => $this->faker->randomFloat(2, 1, 3),
                    'readability' => $this->faker->randomElement(['Good', 'Moderate', 'Needs Improvement']),
                    'suggestions' => $this->faker->randomElements([
                        'Add more headings with keywords',
                        'Increase content length',
                        'Add more internal links',
                        'Improve readability with shorter paragraphs'
                    ], $this->faker->numberBetween(1, 3))
                ]
            ],
            'overall_score' => $overallScore / 100
        ];

        return [
            'content_id' => Content::factory(),
            'page_title_score' => $pageTitleScore,
            'meta_description_score' => $metaDescriptionScore,
            'content_score' => $contentScore,
            'overall_score' => $overallScore,
            'detail_score' => $detailScore,
        ];
    }
} 