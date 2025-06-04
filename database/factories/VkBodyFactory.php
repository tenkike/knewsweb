<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VkBody;
use App\Models\VkMenu;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pbody>
 */
class VkBodyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = VkBody::class;


    public function definition()
    {
         return [
            'id_body'=> VkMenu::factory(),
            'title' => $this->faker->unique()->word(),
            'description' => $this->faker->text,
        ];
    }
}
