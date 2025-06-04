<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VkImage;
use App\Models\VkBody;
use App\Models\VkPortafolio;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pimage>
 */
class VkImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = VkImage::class;

    public function definition()
    {
        
        return [
        //    'id_body'=> $resultBody,
        //    'id_portafolio'=> $resultPort,
            'alt'=> $this->faker->unique()->word(),
            'src'=> 'image21.jpeg',
            'description'=> $this->faker->text
        ];
        
    }
}
