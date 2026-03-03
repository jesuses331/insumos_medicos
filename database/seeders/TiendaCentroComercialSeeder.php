<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiendaCentroComercialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpiar datos previos
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \App\Models\Caja::truncate();
        \App\Models\CashRegister::truncate();
        \App\Models\Sucursal::truncate();
        \App\Models\User::truncate();
        \App\Models\Sale::truncate();
        \App\Models\SaleDetail::truncate();
        \App\Models\Product::truncate();
        \App\Models\Marca::truncate();
        \App\Models\Categoria::truncate();
        \App\Models\TipoRepuesto::truncate();
        \Spatie\Permission\Models\Role::truncate();
        \Spatie\Permission\Models\Permission::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 2. Crear Roles y Permisos
        $roleOwner = \Spatie\Permission\Models\Role::create(['name' => 'dueño']);
        $roleAdmin = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $roleVendedor = \Spatie\Permission\Models\Role::create(['name' => 'vendedor']);

        $permisos = [
            'ver-tablero',
            'ver-usuarios',
            'seleccionar-sucursal',
            'ver-ventas',
            'crear-ventas',
            'ver-productos',
            'crear-productos',
            'editar-productos',
            'borrar-productos',
            'ver-traslados',
            'crear-traslados',
            'ver-cotizaciones',
            'crear-cotizaciones',
            'ver-reportes-globales',
            'gestionar-cajas'
        ];

        foreach ($permisos as $p) {
            \Spatie\Permission\Models\Permission::create(['name' => $p]);
        }

        // Asignar TODO al Dueño y Admin
        $allPermissions = \Spatie\Permission\Models\Permission::all();
        $roleOwner->givePermissionTo($allPermissions);
        $roleAdmin->givePermissionTo($allPermissions);

        // Estefany (Vendedor) Restringida
        $roleVendedor->givePermissionTo([
            'seleccionar-sucursal',
            'ver-productos',
            'crear-ventas',
            'ver-ventas',
            'ver-cotizaciones',
            'crear-cotizaciones',
            'ver-traslados',
            'crear-traslados'
        ]);

        // 3. Crear las dos Sucursales
        $sucursalAlta = \App\Models\Sucursal::create([
            'nombre' => 'Tienda Planta Alta',
            'direccion' => 'C.C. Principal - Piso 1',
            'telefono' => '123456001'
        ]);

        $sucursalBaja = \App\Models\Sucursal::create([
            'nombre' => 'Tienda Planta Baja',
            'direccion' => 'C.C. Principal - Planta Baja',
            'telefono' => '123456002'
        ]);

        // 4. Crear Usuarios

        // DUEÑO (Acceso a todo)
        $dueño = \App\Models\User::create([
            'name' => 'Dueño de Tienda',
            'email' => 'dueno@tienda.com',
            'password' => \Illuminate\Support\Facades\Hash::make('dueno123'),
        ]);
        $dueño->assignRole($roleOwner);
        $dueño->sucursales()->attach([$sucursalAlta->id, $sucursalBaja->id]);

        // Ingrid - Administradora
        $ingrid = \App\Models\User::create([
            'name' => 'Ingrid',
            'email' => 'ingrid@tienda.com',
            'password' => \Illuminate\Support\Facades\Hash::make('ingrid123'),
        ]);
        $ingrid->assignRole($roleAdmin);
        $ingrid->sucursales()->attach([$sucursalAlta->id, $sucursalBaja->id]);

        // Estefany - Vendedora
        $estefany = \App\Models\User::create([
            'name' => 'Estefany',
            'email' => 'estefany@tienda.com',
            'password' => \Illuminate\Support\Facades\Hash::make('estefany123'),
        ]);
        $estefany->assignRole($roleVendedor);
        $estefany->sucursales()->attach($sucursalBaja->id);

        // 5. Crear Estaciones de Caja
        \App\Models\Caja::create(['sucursal_id' => $sucursalAlta->id, 'nombre' => 'Caja Planta Alta', 'estado' => 'Activo']);
        \App\Models\Caja::create(['sucursal_id' => $sucursalBaja->id, 'nombre' => 'Caja Planta Baja', 'estado' => 'Activo']);

        // 6. Algunos Productos para pruebas (Médicos)
        $productos = [
            ['nombre_generico' => 'Paracetamol', 'nombre_comercial' => 'Panadol 500mg', 'unidad_medida' => 'Blister', 'costo' => 0.50, 'precio_venta' => 1.50, 'categoria' => 'Farmacia'],
            ['nombre_generico' => 'Guantes de Nitrilo', 'nombre_comercial' => 'Guantes Quirúrgicos M', 'unidad_medida' => 'Caja', 'costo' => 5.00, 'precio_venta' => 12.00, 'categoria' => 'Descartables'],
            ['nombre_generico' => 'Bisturí #11', 'nombre_comercial' => 'Hoja de Bisturí Estéril', 'unidad_medida' => 'Unidad', 'costo' => 0.20, 'precio_venta' => 0.80, 'categoria' => 'Quirúrgicos'],
            ['nombre_generico' => 'Tensiómetro Digital', 'nombre_comercial' => 'Omron M3', 'unidad_medida' => 'Unidad', 'costo' => 45.00, 'precio_venta' => 85.00, 'categoria' => 'Equipamiento'],
        ];

        foreach ($productos as $pData) {
            $categoria = \App\Models\Categoria::firstOrCreate(['nombre' => $pData['categoria']]);

            $product = \App\Models\Product::create([
                'categoria_id' => $categoria->id,
                'nombre_generico' => $pData['nombre_generico'],
                'nombre_comercial' => $pData['nombre_comercial'],
                'unidad_medida' => $pData['unidad_medida'],
                'costo' => $pData['costo'],
                'precio_venta' => $pData['precio_venta'],
            ]);

            // Lote 1: Vence pronto
            $product->sucursales()->attach($sucursalAlta->id, [
                'stock' => 50,
                'lote' => 'LOTE-A1',
                'fecha_vencimiento' => now()->addDays(15)
            ]);

            // Lote 2: Vence después
            $product->sucursales()->attach($sucursalBaja->id, [
                'stock' => 100,
                'lote' => 'LOTE-B2',
                'fecha_vencimiento' => now()->addDays(120)
            ]);
        }
    }
}
