<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['tenant_id', 'branch_id', 'user_id', 'customer_id', 'number', 'total', 'payment_method', 'status', 'notes'];
}
