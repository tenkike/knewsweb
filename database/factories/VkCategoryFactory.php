<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\VkCategory;
use App\Models\VkSubCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VkCategory>
 */
class VkCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = VkCategory::class;


    public function definition()
    {
         return [
            'categoryName' => $this->faker->unique()->name,
            
        ];
    }
}
