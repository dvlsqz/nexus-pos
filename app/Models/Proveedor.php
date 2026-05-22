<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'nit', 'nombre', 'nombre_comercial', 'contacto_nombre',
        'telefono', 'telefono_alt', 'email', 'whatsapp', 'website',
        'pais', 'departamento', 'municipio', 'direccion',
        'credito_dias', 'descuento_default', 'moneda',
        'activo', 'notas',
    ];

    protected $casts = [
        'activo'            => 'boolean',
        'descuento_default' => 'decimal:2',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeBuscar($query, string $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('nit', 'like', "%{$termino}%")
              ->orWhere('contacto_nombre', 'like', "%{$termino}%")
              ->orWhere('telefono', 'like', "%{$termino}%");
        });
    }
}
