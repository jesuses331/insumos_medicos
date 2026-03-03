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
            // New foreign key columns (nullable first for data migration)
            $table->unsignedBigInteger('marca_id')->after('id')->nullable();
            $table->unsignedBigInteger('categoria_id')->after('marca_id')->nullable();
            $table->unsignedBigInteger('tipo_id')->after('categoria_id')->nullable();

            // Add foreign key constraints
            $table->foreign('marca_id')->references('id')->on('marcas')->onDelete('set null');
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('set null');
            $table->foreign('tipo_id')->references('id')->on('tipos_repuesto')->onDelete('set null');
        });

        // Data migration logic
        $products = \Illuminate\Support\Facades\DB::table('products')->get();

        foreach ($products as $product) {
            $marca_id = null;
            if (isset($product->marca) && $product->marca) {
                $marca_id = \Illuminate\Support\Facades\DB::table('marcas')->where('nombre', $product->marca)->value('id');
                if (!$marca_id) {
                    $marca_id = \Illuminate\Support\Facades\DB::table('marcas')->insertGetId([
                        'nombre' => $product->marca,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            $categoria_id = null;
            if (isset($product->categoria) && $product->categoria) {
                $categoria_id = \Illuminate\Support\Facades\DB::table('categorias')->where('nombre', $product->categoria)->value('id');
                if (!$categoria_id) {
                    $categoria_id = \Illuminate\Support\Facades\DB::table('categorias')->insertGetId([
                        'nombre' => $product->categoria,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            $tipo_id = null;
            if (isset($product->tipo) && $product->tipo) {
                $tipo_id = \Illuminate\Support\Facades\DB::table('tipos_repuesto')->where('nombre', $product->tipo)->value('id');
                if (!$tipo_id) {
                    $tipo_id = \Illuminate\Support\Facades\DB::table('tipos_repuesto')->insertGetId([
                        'nombre' => $product->tipo,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Update current product with new IDs
            \Illuminate\Support\Facades\DB::table('products')->where('id', $product->id)->update([
                'marca_id' => $marca_id,
                'categoria_id' => $categoria_id,
                'tipo_id' => $tipo_id,
            ]);
        }

        // Drop old text columns
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['marca', 'categoria', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('marca')->after('marca_id')->nullable();
            $table->string('categoria')->after('categoria_id')->nullable();
            $table->string('tipo')->after('tipo_id')->nullable();
        });

        // Restore data
        $products = \Illuminate\Support\Facades\DB::table('products')->get();
        foreach ($products as $product) {
            \Illuminate\Support\Facades\DB::table('products')->where('id', $product->id)->update([
                'marca' => \Illuminate\Support\Facades\DB::table('marcas')->where('id', $product->marca_id)->value('nombre'),
                'categoria' => \Illuminate\Support\Facades\DB::table('categorias')->where('id', $product->categoria_id)->value('nombre'),
                'tipo' => \Illuminate\Support\Facades\DB::table('tipos_repuesto')->where('id', $product->tipo_id)->value('nombre'),
            ]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['marca_id']);
            $table->dropForeign(['categoria_id']);
            $table->dropForeign(['tipo_id']);
            $table->dropColumn(['marca_id', 'categoria_id', 'tipo_id']);
        });
    }
};
