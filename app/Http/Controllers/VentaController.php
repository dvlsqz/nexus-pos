<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VentaController extends Controller
{
    public function __construct(protected VentaService $ventaService) {}

    // Lista de ventas
    public function index(Request $request)
    {
        $ventas = Venta::with(['cliente', 'user'])
            ->when($request->buscar, function ($q, $buscar) {
                $q->where('numero', 'like', "%{$buscar}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$buscar}%"));
            })
            ->when($request->estado, fn($q, $v) => $q->where('estado', $v))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Ventas/Index', [
            'ventas'  => $ventas,
            'filtros' => $request->only(['buscar', 'estado']),
        ]);
    }

    // Pantalla POS
    public function pos()
    {
        return Inertia::render('Ventas/Pos', [
            'clientes'  => Cliente::activos()->orderBy('nombre')->get(['id', 'nombre', 'nit']),
            'productos' => Producto::with('categoria')
                ->enVenta()
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'codigo', 'precio_venta', 'stock_actual',
                       'aplica_iva', 'porcentaje_iva', 'unidad_medida', 'categoria_id']),
        ]);
    }

    // Guardar venta desde POS
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'     => 'required|exists:clientes,id',
            'tipo_pago'      => 'required|in:CONTADO,CREDITO',
            'metodo_pago'    => 'required|string',
            'tipo_documento' => 'required|in:COMPROBANTE,FACTURA',
            'monto_pagado'   => 'required|numeric|min:0',
            'items'          => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad'    => 'required|numeric|min:0.0001',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        try {
            $venta = $this->ventaService->crear(
                $request->only(['cliente_id', 'tipo_pago', 'metodo_pago',
                               'tipo_documento', 'monto_pagado', 'observaciones']),
                $request->items,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'venta'   => $venta->load(['items.producto', 'cliente']),
                'numero'  => $venta->numero,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // Anular venta
    public function anular(Request $request, Venta $venta)
    {
        $request->validate([
            'motivo' => 'required|string|min:10',
        ]);

        try {
            $this->ventaService->anular($venta, $request->motivo, auth()->id());
            return back()->with('success', 'Venta anulada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Venta $venta)
    {
        return Inertia::render('Ventas/Show', [
            'venta' => $venta->load(['items.producto', 'cliente', 'user']),
        ]);
    }
}
