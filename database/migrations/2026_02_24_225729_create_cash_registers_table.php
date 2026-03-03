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
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->dateTime('fecha_apertura');
            $table->decimal('monto_apertura', 10, 2);
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('monto_cierre', 10, 2)->nullable();
            $table->decimal('total_ventas', 10, 2)->default(0);
            $table->enum('status', ['Abierta', 'Cerrada'])->default('Abierta');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
