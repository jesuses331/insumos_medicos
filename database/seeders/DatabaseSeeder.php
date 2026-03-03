<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sucursal;
use App\Models\Product;
use App\Models\User;
use App\Models\Transfer;
use App\Models\TransferDetail;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Sale;
use App\Models\SaleDetail;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar para evitar duplicados
        Schema::disableForeignKeyConstraints();
        DB::table('product_branch')->truncate();
        \App\Models\Marca::truncate();
        \App\Models\Categoria::truncate();
        \App\Models\TipoRepuesto::truncate();
        TransferDetail::truncate();
        Transfer::truncate();
        SaleDetail::truncate();
        Sale::truncate();
        Product::truncate();
        Sucursal::truncate();
        User::truncate();
        Role::query()->delete();
        Permission::query()->delete();
        Schema::enableForeignKeyConstraints();

        // Ejecutar el Escenario de Negocio Único (Dueño, Ingrid y Estefany)
        $this->call(TiendaCentroComercialSeeder::class);
    }
}
