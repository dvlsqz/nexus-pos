<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaProducto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categorias_productos';

    protected $fillable = ['nombre', 'descripcion', 'color', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
