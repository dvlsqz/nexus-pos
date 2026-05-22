<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Inertia\Inertia;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::query()
            ->when($request->buscar, fn($q) => $q->buscar($request->buscar))
            ->when($request->estado, function ($q, $estado) {
                $q->where('activo', $estado === 'activo');
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes,
            'filtros'  => $request->only(['buscar', 'estado']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Clientes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo'             => 'required|in:NATURAL,JURIDICA',
            'nit'              => 'nullable|string|max:20',
            'cui'              => 'nullable|string|max:15',
            'nombre'           => 'required|string|max:200',
            'nombre_comercial' => 'nullable|string|max:200',
            'telefono'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:150',
            'whatsapp'         => 'nullable|string|max:20',
            'departamento'     => 'nullable|string|max:100',
            'municipio'        => 'nullable|string|max:100',
            'zona'             => 'nullable|string|max:10',
            'direccion'        => 'nullable|string',
            'credito_limite'   => 'nullable|numeric|min:0',
            'credito_dias'     => 'nullable|integer|min:0',
            'descuento_default'=> 'nullable|numeric|min:0|max:100',
            'notas'            => 'nullable|string',
        ]);

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return Inertia::render('Clientes/Edit', [
            'cliente' => $cliente,
        ]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'tipo'             => 'required|in:NATURAL,JURIDICA',
            'nit'              => 'nullable|string|max:20',
            'cui'              => 'nullable|string|max:15',
            'nombre'           => 'required|string|max:200',
            'nombre_comercial' => 'nullable|string|max:200',
            'telefono'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:150',
            'whatsapp'         => 'nullable|string|max:20',
            'departamento'     => 'nullable|string|max:100',
            'municipio'        => 'nullable|string|max:100',
            'zona'             => 'nullable|string|max:10',
            'direccion'        => 'nullable|string',
            'credito_limite'   => 'nullable|numeric|min:0',
            'credito_dias'     => 'nullable|integer|min:0',
            'descuento_default'=> 'nullable|numeric|min:0|max:100',
            'activo'           => 'boolean',
            'notas'            => 'nullable|string',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
