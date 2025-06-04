<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkHeader extends Model
{
    use HasFactory;

    protected $table = 'vk_headers';
    protected $fillable = ['title', 'description'];

    public function Title()
    {
        return $this->belongsTo(VkMenu::class);
    }
}
