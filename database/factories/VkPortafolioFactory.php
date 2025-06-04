<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VkPortafolio;
use App\Models\VkCategory;
use App\Models\VkSubCategory;
use App\Models\VkMenu;
use App\Models\VkBody;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VkPortafolio>
 */
class VkPortafolioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = VkPortafolio::class;

    public function definition()
    {
        return [
            'id_category_port'=> VkCategory::factory(),
            'id_subcat_port' => VkSubCategory::factory(),
            'id_menu_port'=> VkMenu::factory(),
            'id_body_port'=> VkBody::factory(),
            'status'=> $this->faker->randomElement([
                '0', '1'
            ]),
            'title'=> $this->faker->unique()->word(),
            'subtitle'=> $this->faker->word(),
            'description'=> $this->faker->text,
            'name_seo'=> $this->faker->text,
        ];
    }
}
