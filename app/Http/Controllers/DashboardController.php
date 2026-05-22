<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy      = now()->toDateString();
        $esteMes  = now()->startOfMonth()->toDateString();
        $mesAnterior = now()->subMonth()->startOfMonth()->toDateString();
        $finMesAnterior = now()->subMonth()->endOfMonth()->toDateString();

        // Ventas del día
        $ventasHoy = Venta::where('fecha', $hoy)
            ->where('estado', 'COMPLETADA')
            ->selectRaw('COUNT(*) as total_ventas, COALESCE(SUM(total), 0) as total_monto')
            ->first();

        // Ventas del mes
        $ventasMes = Venta::whereBetween('fecha', [$esteMes, $hoy])
            ->where('estado', 'COMPLETADA')
            ->selectRaw('COUNT(*) as total_ventas, COALESCE(SUM(total), 0) as total_monto')
            ->first();

        // Ventas mes anterior para comparar
        $ventasMesAnterior = Venta::whereBetween('fecha', [$mesAnterior, $finMesAnterior])
            ->where('estado', 'COMPLETADA')
            ->selectRaw('COALESCE(SUM(total), 0) as total_monto')
            ->first();

        // Productos con stock bajo
        $stockBajo = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')
            ->where('controla_inventario', true)
            ->where('activo', true)
            ->count();

        // Totales generales
        $totalClientes   = Cliente::where('activo', true)->count();
        $totalProductos  = Producto::where('activo', true)->count();
        $totalProveedores = Proveedor::where('activo', true)->count();

        // Últimas 5 ventas
        $ultimasVentas = Venta::with(['cliente', 'user'])
            ->where('estado', 'COMPLETADA')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Ventas por día últimos 7 días
        $ventasSemana = Venta::where('estado', 'COMPLETADA')
            ->whereBetween('fecha', [now()->subDays(6)->toDateString(), $hoy])
            ->selectRaw('fecha, COUNT(*) as cantidad, SUM(total) as monto')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Top 5 productos más vendidos del mes
        $topProductos = DB::table('venta_items')
            ->join('ventas', 'ventas.id', '=', 'venta_items.venta_id')
            ->join('productos', 'productos.id', '=', 'venta_items.producto_id')
            ->where('ventas.estado', 'COMPLETADA')
            ->whereBetween('ventas.fecha', [$esteMes, $hoy])
            ->selectRaw('productos.nombre, SUM(venta_items.cantidad) as total_cantidad, SUM(venta_items.total) as total_monto')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_monto')
            ->limit(5)
            ->get();

        // Productos con stock bajo detalle
        $productosStockBajo = Producto::with('categoria')
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->where('controla_inventario', true)
            ->where('activo', true)
            ->orderBy('stock_actual')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'ventas_hoy'          => [
                    'cantidad' => $ventasHoy->total_ventas,
                    'monto'    => $ventasHoy->total_monto,
                ],
                'ventas_mes'          => [
                    'cantidad' => $ventasMes->total_ventas,
                    'monto'    => $ventasMes->total_monto,
                ],
                'ventas_mes_anterior' => $ventasMesAnterior->total_monto,
                'stock_bajo'          => $stockBajo,
                'total_clientes'      => $totalClientes,
                'total_productos'     => $totalProductos,
                'total_proveedores'   => $totalProveedores,
            ],
            'ultimas_ventas'       => $ultimasVentas,
            'ventas_semana'        => $ventasSemana,
            'top_productos'        => $topProductos,
            'productos_stock_bajo' => $productosStockBajo,
        ]);
    }
}