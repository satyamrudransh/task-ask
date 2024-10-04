<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $table = 'sub_categories';
    protected $primaryKey = 'id'; 

    protected $fillable = ['name', 'status','category_id']; // Mass assignable attributes



    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Many-to-Many relationship with Product
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_subcategory', 'subcategory_id', 'product_id');
    }
}
