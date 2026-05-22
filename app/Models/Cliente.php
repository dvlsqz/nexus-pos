<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'tipo', 'nit', 'cui', 'nombre', 'nombre_comercial',
        'telefono', 'email', 'whatsapp',
        'departamento', 'municipio', 'zona', 'direccion',
        'credito_limite', 'credito_dias', 'descuento_default',
        'activo', 'notas',
    ];

    protected $casts = [
        'activo'           => 'boolean',
        'credito_limite'   => 'decimal:2',
        'descuento_default'=> 'decimal:2',
    ];

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeBuscar($query, string $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('nit', 'like', "%{$termino}%")
              ->orWhere('telefono', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%");
        });
    }

    // Helpers
    public function getNitFormateadoAttribute(): string
    {
        if (!$this->nit) return 'CF';
        $nit = preg_replace('/[^0-9K]/', '', strtoupper($this->nit));
        if (strlen($nit) > 1) {
            return substr($nit, 0, -1) . '-' . substr($nit, -1);
        }
        return $nit;
    }
}
