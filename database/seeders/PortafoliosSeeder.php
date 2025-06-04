<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VkPortafolio;

class PortafoliosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $model = VkPortafolio::class;

    public function run()
    {
        VkPortafolio::factory()->count(5)->create();    
    }
}
