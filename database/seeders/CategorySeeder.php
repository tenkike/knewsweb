<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VkCategory;
use App\Models\VkSubCategory;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    protected $model = VkCategory::class;


    public function run()
    {
        // Create 10 records of customers
        VkCategory::factory()->count(10)->create();
    }
}
