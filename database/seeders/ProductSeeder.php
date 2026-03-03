<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Sucursal;

class ProductSeeder extends Seeder
{
    // Helper: pick a random element from an array
    private function randomElement(array $array): mixed
    {
        return $array[array_rand($array)];
    }

    // Helper: random float between $min and $max with $decimals places
    private function randomFloat(int $decimals, float $min, float $max): float
    {
        return round($min + mt_rand() / mt_getrandmax() * ($max - $min), $decimals);
    }

    // Helper: random int between $min and $max
    private function numberBetween(int $min, int $max): int
    {
        return rand($min, $max);
    }

    // Helper: generate a string like 'Model AB-123'
    private function bothify(string $pattern): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        foreach (str_split($pattern) as $char) {
            if ($char === '?') {
                $result .= $letters[rand(0, 25)];
            } elseif ($char === '#') {
                $result .= rand(0, 9);
            } else {
                $result .= $char;
            }
        }
        return $result;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sucursales = Sucursal::all();

        if ($sucursales->isEmpty()) {
            $this->command->error('No hay sucursales registradas. Por favor, crea sucursales primero.');
            return;
        }

        $marcas = ['Samsung', 'iPhone', 'Xiaomi', 'Motorola', 'Huawei', 'Oppo', 'Vivo', 'Realme', 'Google'];
        $tiposPantalla = ['Incell', 'OLED', 'AMOLED', 'TFT', 'Original', 'High Copy', 'Calidad AAA'];
        $accesorios = ['Cargador 20W', 'Cable USB-C', 'Protector Cerámico', 'Funda Silicona', 'Audífonos Bluetooth', 'Batería Reemplazo', 'Vidrio Templado'];

        $totalProductos = 300;

        for ($i = 0; $i < $totalProductos; $i++) {
            $categoria_input = $this->randomElement(['pantalla', 'accesorio']);
            $categoria = \App\Models\Categoria::firstOrCreate(['nombre' => $categoria_input]);

            if ($categoria_input === 'pantalla') {
                $marca_name = $this->randomElement($marcas);
                $modelo = $this->bothify('Model ??-###');
                $tipo_name = $this->randomElement($tiposPantalla);
                $costo = $this->randomFloat(2, 15, 180);
                $precioVenta = $costo * $this->randomFloat(2, 1.4, 2.2);
            } else {
                $tipo_name = null;
                $marca_name = $this->randomElement($marcas);
                $modelo = $this->bothify('Series #');
                $costo = $this->randomFloat(2, 2, 25);
                $precioVenta = $costo * $this->randomFloat(2, 2, 4);
            }

            $marca = \App\Models\Marca::firstOrCreate(['nombre' => $marca_name]);
            $tipoId = $tipo_name ? \App\Models\TipoRepuesto::firstOrCreate(['nombre' => $tipo_name])->id : null;

            $product = Product::create([
                'marca_id' => $marca->id,
                'modelo' => $modelo,
                'tipo_id' => $tipoId,
                'costo' => round($costo, 2),
                'precio_venta' => round($precioVenta, 2),
                'categoria_id' => $categoria->id,
            ]);

            // Asignar stock aleatorio a cada sucursal
            foreach ($sucursales as $sucursal) {
                $product->sucursales()->attach($sucursal->id, [
                    'stock' => $this->numberBetween(0, 50),
                ]);
            }
        }

        $this->command->info("$totalProductos productos creados exitosamente.");
    }
}

