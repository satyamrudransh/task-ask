<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPdf extends Model
{
    use HasFactory;
    protected $table = 'product_pdf';

    protected $fillable = [
        'file_path','product_id','heading'
        
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
