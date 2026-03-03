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
        Schema::table('product_branch', function (Blueprint $table) {
            $table->string('lote')->nullable()->after('branch_id');
            $table->date('fecha_vencimiento')->nullable()->after('lote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_branch', function (Blueprint $table) {
            //
        });
    }
};
