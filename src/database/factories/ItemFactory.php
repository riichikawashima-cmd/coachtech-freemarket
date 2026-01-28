<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'name'        => $this->faker->word(),
            'brand'       => null,
            'description' => $this->faker->sentence(),
            'price'       => $this->faker->numberBetween(100, 10000),
            'condition'   => 1, // 1=新品想定（enum/定数化は後で）
            'image_path'  => 'images/sample.jpg',
        ];
    }
}
