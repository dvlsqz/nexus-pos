<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'categoria_id', 'codigo', 'codigo_barras', 'nombre', 'descripcion',
        'imagen', 'unidad_medida', 'precio_compra', 'precio_venta',
        'precio_mayoreo', 'aplica_iva', 'porcentaje_iva', 'stock_actual',
        'stock_minimo', 'activo', 'se_vende', 'se_compra',
        'controla_inventario', 'bien_servicio',
    ];

    protected $casts = [
        'aplica_iva'          => 'boolean',
        'activo'              => 'boolean',
        'se_vende'            => 'boolean',
        'se_compra'           => 'boolean',
        'controla_inventario' => 'boolean',
        'precio_compra'       => 'decimal:4',
        'precio_venta'        => 'decimal:4',
        'precio_mayoreo'      => 'decimal:4',
        'porcentaje_iva'      => 'decimal:2',
        'stock_actual'        => 'decimal:4',
        'stock_minimo'        => 'decimal:4',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
    }

    // Precio sin IVA
    public function getPrecioSinIvaAttribute(): float
    {
        if (!$this->aplica_iva) return (float) $this->precio_venta;
        return round($this->precio_venta / (1 + $this->porcentaje_iva / 100), 4);
    }

    // Monto de IVA
    public function getMontoIvaAttribute(): float
    {
        if (!$this->aplica_iva) return 0.0;
        return round($this->precio_venta - $this->precio_sin_iva, 4);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeEnVenta($query)
    {
        return $query->where('se_vende', true)->where('activo', true);
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock_actual', '<=', 'stock_minimo')
                     ->where('controla_inventario', true);
    }

    public function scopeBuscar($query, string $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('codigo', 'like', "%{$termino}%")
              ->orWhere('codigo_barras', 'like', "%{$termino}%");
        });
    }

    public function estaEnStockBajo(): bool
    {
        return $this->controla_inventario && $this->stock_actual <= $this->stock_minimo;
    }

    public static function generarCodigo(): string
    {
        $ultimo = static::withTrashed()->max('id') ?? 0;
        return 'PROD-' . str_pad($ultimo + 1, 5, '0', STR_PAD_LEFT);
    }
}
