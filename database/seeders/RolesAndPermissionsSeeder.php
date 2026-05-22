<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos por módulo
        $permisos = [
            'productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar',
            'inventario.ver', 'inventario.ajustar',
            'ventas.ver', 'ventas.crear', 'ventas.anular', 'ventas.ver_todas',
            'cotizaciones.ver', 'cotizaciones.crear', 'cotizaciones.editar', 'cotizaciones.convertir',
            'compras.ver', 'compras.crear', 'compras.recibir', 'compras.anular',
            'clientes.ver', 'clientes.crear', 'clientes.editar', 'clientes.eliminar',
            'proveedores.ver', 'proveedores.crear', 'proveedores.editar', 'proveedores.eliminar',
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            'reportes.ventas', 'reportes.inventario', 'reportes.compras',
            'caja.abrir', 'caja.cerrar', 'caja.ver',
            'configuracion.ver', 'configuracion.editar',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $admin->syncPermissions(Permission::all());

        $gerente = Role::firstOrCreate(['name' => 'Gerente']);
        $gerente->syncPermissions(Permission::whereNotIn('name', [
            'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            'configuracion.editar',
        ])->get());

        $vendedor = Role::firstOrCreate(['name' => 'Vendedor']);
        $vendedor->syncPermissions([
            'productos.ver',
            'inventario.ver',
            'ventas.ver', 'ventas.crear',
            'cotizaciones.ver', 'cotizaciones.crear', 'cotizaciones.editar', 'cotizaciones.convertir',
            'clientes.ver', 'clientes.crear', 'clientes.editar',
            'caja.abrir', 'caja.cerrar', 'caja.ver',
            'reportes.ventas',
        ]);

        $bodeguero = Role::firstOrCreate(['name' => 'Bodeguero']);
        $bodeguero->syncPermissions([
            'productos.ver', 'productos.editar',
            'inventario.ver', 'inventario.ajustar',
            'compras.ver', 'compras.recibir',
            'proveedores.ver',
            'reportes.inventario',
        ]);

        // Usuario administrador inicial
        $user = User::firstOrCreate(
            ['email' => 'admin@nexuspos.gt'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('Admin2026!'),
            ]
        );
        $user->assignRole($admin);

        $this->command->info('✅ Roles y permisos creados.');
        $this->command->info('📧 admin@nexuspos.gt  🔑 Admin2026!');
    }
}
