<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkCategory extends Model
{
    use HasFactory;

   protected $table = 'vk_categories';

   protected $fillable = ['categoryName'];


    public function subCategories(){
        return $this->hasOne(VkSubCategory::class, 'id_category', 'id');
    }
    
    public function Title()
    {
        return $this->belongsTo(VkMenu::class, 'id_name', 'id');
    }
}
