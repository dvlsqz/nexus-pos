<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['name', 'slug', 'nit', 'currency', 'settings', 'is_active'];

    public function branches() { 
        return $this->hasMany(Branch::class); 
    }
}
