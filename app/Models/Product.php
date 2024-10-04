<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
    {
    use HasFactory;
    protected $table = 'product';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'image',
        'short_description',
        'features',
        'status'
    ];

    // Many-to-Many relationship with SubCategory
    public function subcategories()
        {
        return $this->belongsToMany(SubCategory::class, 'product_subcategory', 'product_id', 'subcategory_id');
        }

    public function pdfs()
        {
        return $this->hasMany(ProductPdf::class);
        }

    public function titles()
        {
        return $this->hasMany(ProductTitle::class);
        }


    }
