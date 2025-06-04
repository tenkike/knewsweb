<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkFooter extends Model
{
    use HasFactory;

    protected $table = 'vk_footers';
    protected $fillable = ['title', 'description'];

    public function Title()
    {
        return $this->belongsTo(VkMenu::class);
    }
}
