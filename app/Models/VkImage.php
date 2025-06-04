<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class VkImage extends Model
{
    use HasFactory;

   protected $table = 'vk_images';
   protected $fillable = ['alt', 'description'];

   public function ImageBody()
    {
        return $this->belongsTo(VkBody::class);
    }

    public function ImagePortafolio()
    {
        return $this->belongsTo(VkPortafolio::class);
    }
}
