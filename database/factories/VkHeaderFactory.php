<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VkHeader;
use App\Models\VkMenu;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pheader>
 */
class VkHeaderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = VkHeader::class;

    public function definition()
    {
        return [
            'id_header'=> VkMenu::factory(),
            'title' => $this->faker->unique()->word(),
            'description' => $this->faker->text,
        ];
    }
}
