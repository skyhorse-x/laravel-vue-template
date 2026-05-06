<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $categories = ['技术', '生活', '旅游', '美食', '科技', '教育', '健康', '娱乐'];
        $covers = [
            'https://picsum.photos/seed/article1/800/400',
            'https://picsum.photos/seed/article2/800/400',
            'https://picsum.photos/seed/article3/800/400',
            'https://picsum.photos/seed/article4/800/400',
            'https://picsum.photos/seed/article5/800/400',
        ];

        return [
            'title' => fake()->sentence(8),
            'slug' => fake()->unique()->slug(3),
            'content' => fake()->paragraphs(5, true),
            'cover' => fake()->randomElement($covers),
            'category' => fake()->randomElement($categories),
            'status' => fake()->randomElement([0, 1]),
            'view_count' => fake()->numberBetween(0, 5000),
            'sort' => fake()->numberBetween(0, 100),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }
}
