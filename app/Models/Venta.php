<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero', 'fecha', 'cliente_id', 'user_id',
        'subtotal', 'descuento_total', 'base_imponible',
        'iva', 'exento', 'total',
        'tipo_pago', 'metodo_pago', 'monto_pagado', 'cambio',
        'estado', 'tipo_documento', 'motivo_anulacion',
        'anulada_at', 'anulada_por', 'observaciones',
        'fel_uuid', 'fel_serie', 'fel_numero',
    ];

    protected $casts = [
        'fecha'      => 'date',
        'anulada_at' => 'datetime',
        'subtotal'   => 'decimal:2',
        'iva'        => 'decimal:2',
        'total'      => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'cambio'     => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }

    public function anuladaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anulada_por');
    }

    public static function generarNumero(): string
    {
        $anio   = now()->year;
        $ultimo = static::whereYear('fecha', $anio)->count();
        return 'VTA-' . $anio . '-' . str_pad($ultimo + 1, 6, '0', STR_PAD_LEFT);
    }

    public function recalcularTotales(): void
    {
        $items         = $this->items;
        $subtotal      = 0;
        $descuento     = 0;
        $baseGravada   = 0;
        $totalIva      = 0;
        $totalExento   = 0;

        foreach ($items as $item) {
            $subtotal  += $item->subtotal;
            $descuento += $item->descuento_monto;

            if ($item->aplica_iva) {
                $baseGravada += $item->subtotal;
                $totalIva    += $item->monto_iva;
            } else {
                $totalExento += $item->subtotal;
            }
        }

        $this->update([
            'subtotal'        => $subtotal,
            'descuento_total' => $descuento,
            'base_imponible'  => $baseGravada,
            'iva'             => $totalIva,
            'exento'          => $totalExento,
            'total'           => $baseGravada + $totalIva + $totalExento,
        ]);
    }
}