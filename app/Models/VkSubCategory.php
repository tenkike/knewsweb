<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkSubCategory extends Model
{
    use HasFactory;

    protected $table = 'vk_subcategories';

    protected $fillable = ['id_category', 'subcatName'];


    //public function Category(){
      //  return $this->hasOne(VkCategory::class, 'subcategories_id', 'categoryId');
    //}

        
     public function Category(){

        return $this->belongsTo(VkCategory::class, 'id', 'id_category');
    }

    public function Title()
    {
        return $this->belongsTo(VkMenu::class, 'id_sublink', 'id');
    }
    
}
