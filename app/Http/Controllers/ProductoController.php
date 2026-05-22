<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::with('categoria')
            ->when($request->buscar, fn($q) => $q->buscar($request->buscar))
            ->when($request->categoria_id, fn($q, $v) => $q->where('categoria_id', $v))
            ->when($request->estado, function ($q, $estado) {
                $q->where('activo', $estado === 'activo');
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Productos/Index', [
            'productos'  => $productos,
            'categorias' => CategoriaProducto::activos()->orderBy('nombre')->get(),
            'filtros'    => $request->only(['buscar', 'categoria_id', 'estado']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Productos/Create', [
            'categorias'     => CategoriaProducto::activos()->orderBy('nombre')->get(),
            'codigoSugerido' => Producto::generarCodigo(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id'       => 'required|exists:categorias_productos,id',
            'codigo'             => 'required|string|max:50|unique:productos,codigo',
            'codigo_barras'      => 'nullable|string|max:100|unique:productos,codigo_barras',
            'nombre'             => 'required|string|max:200',
            'descripcion'        => 'nullable|string',
            'unidad_medida'      => 'required|string|max:30',
            'precio_compra'      => 'required|numeric|min:0',
            'precio_venta'       => 'required|numeric|min:0',
            'precio_mayoreo'     => 'nullable|numeric|min:0',
            'aplica_iva'         => 'boolean',
            'porcentaje_iva'     => 'numeric|min:0|max:100',
            'stock_actual'       => 'required|numeric|min:0',
            'stock_minimo'       => 'required|numeric|min:0',
            'activo'             => 'boolean',
            'se_vende'           => 'boolean',
            'se_compra'          => 'boolean',
            'controla_inventario'=> 'boolean',
            'bien_servicio'      => 'in:B,S',
        ]);

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return Inertia::render('Productos/Edit', [
            'producto'   => $producto->load('categoria'),
            'categorias' => CategoriaProducto::activos()->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'categoria_id'       => 'required|exists:categorias_productos,id',
            'codigo'             => "required|string|max:50|unique:productos,codigo,{$producto->id}",
            'codigo_barras'      => "nullable|string|max:100|unique:productos,codigo_barras,{$producto->id}",
            'nombre'             => 'required|string|max:200',
            'descripcion'        => 'nullable|string',
            'unidad_medida'      => 'required|string|max:30',
            'precio_compra'      => 'required|numeric|min:0',
            'precio_venta'       => 'required|numeric|min:0',
            'precio_mayoreo'     => 'nullable|numeric|min:0',
            'aplica_iva'         => 'boolean',
            'porcentaje_iva'     => 'numeric|min:0|max:100',
            'stock_minimo'       => 'required|numeric|min:0',
            'activo'             => 'boolean',
            'se_vende'           => 'boolean',
            'se_compra'          => 'boolean',
            'controla_inventario'=> 'boolean',
            'bien_servicio'      => 'in:B,S',
        ]);

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
