<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VkImage;    

class PimageSeeder extends Seeder
{
    protected $model = VkImage::class;

    public function run()
    {
        $dataBody = 2000;
        $dataPort = 6020;
        
        for ($i = 0; $i < $dataPort; $i++) {
            $resultPort = ['id_portafolio' => $i + 6020]; // Assuming 'id_portafolio' starts from 6001
            VkImage::factory()->create($resultPort);
            if ($i + 6000 >= 6030) {
                break; // Break the loop when 'id_portafolio' reaches 6010
            }
        }

        for ($i = 0; $i < $dataBody; $i++) {
            $resultBody = ['id_body' => $i + 2000]; // Assuming 'id_body' increments from 1
            VkImage::factory()->create($resultBody);
            if ($i + 1 >= 2010) {
                break; // Break the loop when 'id_body' reaches 1010
            }
        }

        
    }
}

