<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'display_name'  => $this->faker->name(),
            'postal_code'   => '123-4567',
            'address'       => '東京都テスト区1-2-3',
            'building_name' => 'テストビル101',
        ];
    }
}
