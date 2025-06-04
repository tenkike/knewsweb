<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VkMenu;
use App\Models\VkCategory;
use App\Models\VkSubCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ptitle>
 */
class VkMenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = VkMenu::class;

    public function definition()
    {
    
        return [

            'status' => $this->faker->randomElement([
                '0', '1'
            ]),

            'title' => $this->faker->unique()->word(),
            'id_name' => VkCategory::factory(),
            'id_sublink' => VkSubCategory::factory(),        
            
        ];

    }

   /* $factory->define(Pbody::class, function (Factory $faker) {
        return [
            'title' => $faker->title,
            'description' => $faker->description,
        ];  
    });

    $factory->define(Pheader::class, function (Factory $faker) {
        return [
            'title' => $faker->title,
            'description' => $faker->description,
        ];  
    });

    $factory->define(Pfooter::class, function (Factory $faker) {
        return [
            'title' => $faker->title,
            'description' => $faker->description,
        ];  
    });

    $factory->define(Pimage::class, function (Factory $faker) {
        return [
            'title' => $faker->title,
            'description' => $faker->description,
        ];  
    });*/
    
    
}
