<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::all();
        $user_count = count($users);
        if ($user_count > 0) {
            $index = rand(0, $user_count - 1);
            $user = $users[$index];
        }
        else {
            $user = User::factory()->create();
        }

        return [
            'title' => $this->faker->title(),
            'price' => rand(100, 1000),
            'user_id' => $user->id
        ];
    }
}
