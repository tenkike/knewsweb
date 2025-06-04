<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\VkSubCategory;
use App\Models\VkCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VkSubCategory>
 */
class VkSubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = VkSubCategory::class;

    public function definition()
    {
       return [
            'id_category'=> VkCategory::factory(),
            'subcatName' => $this->faker->unique()->name,
            
        ];
    }
}
