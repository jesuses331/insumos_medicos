<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function run(): void
    {
        // Ensure ver-sucursales permission exists
        $permission = Permission::where('name', 'ver-sucursales')->first();
        if (!$permission) {
            $permission = Permission::create(['name' => 'ver-sucursales']);
        }

        // Give it to admin and vendedorRoles
        $roleAdmin = Role::where('name', 'admin')->first();
        if ($roleAdmin) {
            $roleAdmin->givePermissionTo($permission);
        }

        $roleVendedor = Role::where('name', 'vendedor')->first();
        if ($roleVendedor) {
            $roleVendedor->givePermissionTo($permission);
        }

        // Also ensure seleccionar-sucursal is given to vendedor if missing
        $selPermission = Permission::where('name', 'seleccionar-sucursal')->first();
        if ($selPermission && $roleVendedor) {
            $roleVendedor->givePermissionTo($selPermission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for business logic permissions usually, but we could remove them
    }
};
