<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = User::with('roles')
            ->when($request->buscar, function ($q, $buscar) {
                $q->where('name', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
            })
            ->when($request->rol, function ($q, $rol) {
                $q->whereHas('roles', fn($q) => $q->where('name', $rol));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
            'roles'    => Role::orderBy('name')->get(),
            'filtros'  => $request->only(['buscar', 'rol']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Usuarios/Create', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'rol'      => 'required|exists:roles,name',
        ], [
            'name.required'     => 'El nombre es obligatorio.',
            'email.required'    => 'El correo es obligatorio.',
            'email.unique'      => 'Este correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'rol.required'      => 'Selecciona un rol.',
        ]);

        $usuario = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'activo'   => true,
        ]);

        $usuario->assignRole($request->rol);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return Inertia::render('Usuarios/Edit', [
            'usuario' => $usuario->load('roles'),
            'roles'   => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|email|unique:users,email,{$usuario->id}",
            'password' => ['nullable', Password::min(8)->letters()->numbers()],
            'rol'      => 'required|exists:roles,name',
            'activo'   => 'boolean',
        ], [
            'name.required'  => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique'   => 'Este correo ya está registrado.',
            'rol.required'   => 'Selecciona un rol.',
        ]);

        $datos = [
            'name'   => $request->name,
            'email'  => $request->email,
            'activo' => $request->activo,
        ];

        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);
        $usuario->syncRoles([$request->rol]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}