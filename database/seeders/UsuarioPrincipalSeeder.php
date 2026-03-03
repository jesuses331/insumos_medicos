<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sucursal;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UsuarioPrincipalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tablas para evitar duplicados en pruebas
        Schema::disableForeignKeyConstraints();
        Role::truncate();
        Permission::truncate();
        User::truncate();
        Sucursal::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Crear Permisos
        $permisos = [
            'ver-tablero',
            'ver-usuarios',
            'crear-usuarios',
            'editar-usuarios',
            'borrar-usuarios',
            'ver-roles',
            'crear-roles',
            'editar-roles',
            'borrar-roles',
            'ver-permisos',
            'ver-sucursales',
            'crear-sucursales',
            'editar-sucursales',
            'borrar-sucursales',
            'seleccionar-sucursal'
        ];

        foreach ($permisos as $permiso) {
            Permission::create(['name' => $permiso]);
        }

        // 2. Crear Roles
        $roleSuperAdmin = Role::create(['name' => 'super-admin']);
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleVendedor = Role::create(['name' => 'vendedor']);

        // Asignar todos los permisos al Super Admin
        $roleSuperAdmin->givePermissionTo(Permission::all());

        // Permisos básicos para Vendedor
        $roleVendedor->givePermissionTo(['ver-tablero', 'seleccionar-sucursal']);

        // 3. Crear Sucursales de Ejemplo
        $sucursal1 = Sucursal::create([
            'nombre' => 'Sucursal Central',
            'direccion' => 'Av. Principal 123',
            'telefono' => '999888777'
        ]);

        $sucursal2 = Sucursal::create([
            'nombre' => 'Sucursal Norte',
            'direccion' => 'Calle Norte 456',
            'telefono' => '111222333'
        ]);

        // 4. Crear Usuarios

        // Super Admin (Tiene acceso a todo y a todas las sucursales)
        $superAdmin = User::create([
            'name' => 'Super Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole($roleSuperAdmin);
        $superAdmin->sucursales()->attach([$sucursal1->id, $sucursal2->id]);

        // Admin de Sucursal Central
        $adminCentral = User::create([
            'name' => 'Admin Central',
            'email' => 'admin.central@example.com',
            'password' => Hash::make('password'),
        ]);
        $adminCentral->assignRole($roleAdmin);
        $adminCentral->sucursales()->attach($sucursal1->id);

        // Vendedor (Pertenece a ambas sucursales)
        $vendedor = User::create([
            'name' => 'Vendedor Demo',
            'email' => 'vendedor@example.com',
            'password' => Hash::make('password'),
        ]);
        $vendedor->assignRole($roleVendedor);
        $vendedor->sucursales()->attach([$sucursal1->id, $sucursal2->id]);
    }
}
