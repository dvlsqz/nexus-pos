<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use Inertia\Inertia;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $proveedores = Proveedor::query()
            ->when($request->buscar, fn($q) => $q->buscar($request->buscar))
            ->when($request->estado, function ($q, $estado) {
                $q->where('activo', $estado === 'activo');
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Proveedores/Index', [
            'proveedores' => $proveedores,
            'filtros'     => $request->only(['buscar', 'estado']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Proveedores/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nit'               => 'nullable|string|max:20',
            'nombre'            => 'required|string|max:200',
            'nombre_comercial'  => 'nullable|string|max:200',
            'contacto_nombre'   => 'nullable|string|max:150',
            'telefono'          => 'nullable|string|max:20',
            'telefono_alt'      => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:150',
            'whatsapp'          => 'nullable|string|max:20',
            'website'           => 'nullable|string|max:200',
            'pais'              => 'nullable|string|max:100',
            'departamento'      => 'nullable|string|max:100',
            'municipio'         => 'nullable|string|max:100',
            'direccion'         => 'nullable|string',
            'credito_dias'      => 'nullable|integer|min:0',
            'descuento_default' => 'nullable|numeric|min:0|max:100',
            'moneda'            => 'in:GTQ,USD',
            'notas'             => 'nullable|string',
        ]);

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        return Inertia::render('Proveedores/Edit', [
            'proveedor' => $proveedor,
        ]);
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'nit'               => 'nullable|string|max:20',
            'nombre'            => 'required|string|max:200',
            'nombre_comercial'  => 'nullable|string|max:200',
            'contacto_nombre'   => 'nullable|string|max:150',
            'telefono'          => 'nullable|string|max:20',
            'telefono_alt'      => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:150',
            'whatsapp'          => 'nullable|string|max:20',
            'website'           => 'nullable|string|max:200',
            'pais'              => 'nullable|string|max:100',
            'departamento'      => 'nullable|string|max:100',
            'municipio'         => 'nullable|string|max:100',
            'direccion'         => 'nullable|string',
            'credito_dias'      => 'nullable|integer|min:0',
            'descuento_default' => 'nullable|numeric|min:0|max:100',
            'moneda'            => 'in:GTQ,USD',
            'activo'            => 'boolean',
            'notas'             => 'nullable|string',
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
