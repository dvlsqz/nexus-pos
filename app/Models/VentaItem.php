<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaItem extends Model
{
    protected $fillable = [
        'venta_id', 'producto_id', 'descripcion',
        'cantidad', 'precio_unitario',
        'descuento_porcentaje', 'descuento_monto',
        'aplica_iva', 'porcentaje_iva', 'monto_iva',
        'subtotal', 'total',
    ];

    protected $casts = [
        'aplica_iva' => 'boolean',
        'cantidad'   => 'decimal:4',
        'subtotal'   => 'decimal:4',
        'total'      => 'decimal:4',
        'monto_iva'  => 'decimal:4',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }
}