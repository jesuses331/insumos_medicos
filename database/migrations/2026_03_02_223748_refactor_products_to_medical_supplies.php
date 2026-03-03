<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('modelo', 'nombre_comercial');
            $table->string('nombre_generico')->nullable()->after('id');
            $table->enum('unidad_medida', ['Caja', 'Unidad', 'Blister', 'Frasco'])->default('Unidad')->after('precio_venta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('nombre_comercial', 'modelo');
            $table->dropColumn(['nombre_generico', 'unidad_medida']);
        });
    }
};
