<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkBody extends Model
{
    use HasFactory;

   protected $table = 'vk_bodys';

   protected $fillable = ['title', 'description'];

   public function Title()
    {
        return $this->belongsTo(VkMenu::class);
    }

    public function Portafolio(){
        return $this->hasOne(VkPortafolio::class, 'id_body_port', 'id');
    }

    public function Images(){
        return $this->hasMany(VkImage::class, 'id_body', 'id');
    }
}
