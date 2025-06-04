<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\VkMenu;
use App\Models\VkBody;
use App\Models\VkPortafolio;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    protected $model = VkMenu::class;

    public function run()
    {
            User::factory()->count(2)->create();
            
            VkMenu::factory()->create([
                'title' => 'home'
            ]);

            VkMenu::factory()->count(5)
            ->hasBody()
            ->hasHeader()
            ->hasFooter()
            ->create();

            VkBody::factory()->count(5)
            ->hasImages()
            ->create();

            VkPortafolio::factory()->count(5)
            ->hasImages()
            ->create();
      
    }
}
