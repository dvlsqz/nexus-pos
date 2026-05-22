<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaItem;
use Illuminate\Support\Facades\DB;

class VentaService
{
    public function crear(array $datos, array $items, int $userId): Venta
    {
        return DB::transaction(function () use ($datos, $items, $userId) {

            // Validar stock
            foreach ($items as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                if ($producto->controla_inventario && $producto->stock_actual < $item['cantidad']) {
                    throw new \Exception(
                        "Stock insuficiente para: {$producto->nombre}. " .
                        "Disponible: {$producto->stock_actual}"
                    );
                }
            }

            // Crear venta
            $venta = Venta::create([
                'numero'         => Venta::generarNumero(),
                'fecha'          => now()->toDateString(),
                'cliente_id'     => $datos['cliente_id'],
                'user_id'        => $userId,
                'tipo_pago'      => $datos['tipo_pago'] ?? 'CONTADO',
                'metodo_pago'    => $datos['metodo_pago'] ?? 'EFECTIVO',
                'tipo_documento' => $datos['tipo_documento'] ?? 'COMPROBANTE',
                'monto_pagado'   => $datos['monto_pagado'] ?? 0,
                'observaciones'  => $datos['observaciones'] ?? null,
                'estado'         => 'COMPLETADA',
                'subtotal'       => 0,
                'base_imponible' => 0,
                'iva'            => 0,
                'exento'         => 0,
                'total'          => 0,
            ]);

            // Crear items
            foreach ($items as $itemData) {
                $producto       = Producto::find($itemData['producto_id']);
                $cantidad       = (float) $itemData['cantidad'];
                $precioConIva   = (float) $itemData['precio_unitario'];
                $descuentoPct   = (float) ($itemData['descuento_porcentaje'] ?? 0);

                $precioSinIva   = $producto->aplica_iva
                    ? round($precioConIva / (1 + $producto->porcentaje_iva / 100), 4)
                    : $precioConIva;

                $subtotalLinea  = round($precioSinIva * $cantidad, 4);
                $descuentoMonto = round($subtotalLinea * $descuentoPct / 100, 4);
                $baseLinea      = $subtotalLinea - $descuentoMonto;
                $montoIva       = $producto->aplica_iva
                    ? round($baseLinea * $producto->porcentaje_iva / 100, 4)
                    : 0;

                VentaItem::create([
                    'venta_id'             => $venta->id,
                    'producto_id'          => $producto->id,
                    'descripcion'          => $producto->nombre,
                    'cantidad'             => $cantidad,
                    'precio_unitario'      => $precioSinIva,
                    'descuento_porcentaje' => $descuentoPct,
                    'descuento_monto'      => $descuentoMonto,
                    'aplica_iva'           => $producto->aplica_iva,
                    'porcentaje_iva'       => $producto->porcentaje_iva,
                    'monto_iva'            => $montoIva,
                    'subtotal'             => $baseLinea,
                    'total'                => $baseLinea + $montoIva,
                ]);

                // Descontar stock
                $producto->decrement('stock_actual', $cantidad);
            }

            $venta->recalcularTotales();

            $cambio = max(0, $venta->monto_pagado - $venta->total);
            $venta->update(['cambio' => $cambio]);

            return $venta->fresh(['items.producto', 'cliente']);
        });
    }

    public function anular(Venta $venta, string $motivo, int $userId): Venta
    {
        if ($venta->estado === 'ANULADA') {
            throw new \Exception('La venta ya está anulada.');
        }

        return DB::transaction(function () use ($venta, $motivo, $userId) {
            foreach ($venta->items as $item) {
                $item->producto->increment('stock_actual', $item->cantidad);
            }

            $venta->update([
                'estado'           => 'ANULADA',
                'motivo_anulacion' => $motivo,
                'anulada_at'       => now(),
                'anulada_por'      => $userId,
            ]);

            return $venta->fresh();
        });
    }
}