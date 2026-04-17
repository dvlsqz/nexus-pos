<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['tenant_id', 'category_id', 'brand_id', 'unit_id', 'name', 'sku', 'barcode', 'description', 'price', 'cost', 'is_active', 'manage_stock'];


    public function category() { 
        return $this->belongsTo(Category::class); 
    }

    public function brand() { 
        return $this->belongsTo(Brand::class); 
    }

    public function unit() { 
        return $this->belongsTo(Unit::class); 
    }
    
}
