<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $dates = ['created_at', 'updated_at']; // Ensure these fields are cast to Carbon instances

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->format('d-m-Y H:i:s'); // Change format as needed
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at->format('d-m-Y H:i:s'); // Change format as needed
    }
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'image',
        'telegram_id'
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }
}
