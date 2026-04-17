<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['tenant_id', 'product_id', 'branch_id', 'quantity', 'min_stock'];
}
