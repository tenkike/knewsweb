<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkPortafolio extends Model
{
    use HasFactory;

    protected $table = 'vk_portafolios';

    protected $fillable = ['id_category_port', 'id_subcat_port', 'id_menu_port', 'id_body_port', 'status', 'title'];

    public function Category(){
        return $this->hasOne(VkCategory::class, 'id', 'id_category_port');
    }

    public function subCategories(){
        return $this->hasOne(VkSubCategory::class, 'id', 'id_subcat_port');
    }

   
    public function Body(){
        return $this->belongsTo(VkBody::class, 'id', 'id_body_port');
    }

    public function Images(){
        return $this->hasMany(VkImage::class, 'id_portafolio', 'id');
    }
}
