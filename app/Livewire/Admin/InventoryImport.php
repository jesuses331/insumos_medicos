<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\Sucursal;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class InventoryImport extends Component
{
    use WithFileUploads;

    public $file;
    public $branch_id;
    public $importing = false;
    public $importMessage = '';
    public $errorCount = 0;
    public $successCount = 0;

    protected $rules = [
        'file' => 'required|max:2048', // Removed explicit mimes for more flexibility if needed
        'branch_id' => 'required|exists:sucursales,id',
    ];

    public function mount()
    {
        // Default to active branch if exists in session
        $this->branch_id = session('active_sucursal_id');
    }

    public function import()
    {
        $this->validate();

        $this->importing = true;
        $this->successCount = 0;
        $this->errorCount = 0;
        $this->importMessage = '';

        $extension = $this->file->getClientOriginalExtension();
        $path = $this->file->getRealPath();

        if (strtolower($extension) === 'xlsx') {
            $rows = \App\Helpers\SimpleXlsxReader::parse($path);
        } else {
            $rows = [];
            if (($handle = fopen($path, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        }

        if (empty($rows)) {
            $this->importMessage = 'Error: No se pudo leer el archivo o está vacío.';
            $this->importing = false;
            return;
        }

        // Expected columns: nombre_generico, nombre_comercial, unidad_medida, costo, precio_venta, categoria, stock
        $header = array_shift($rows);

        if (!$header || count($header) < 7) {
            $this->importMessage = 'Error: Estructura de archivo inválida. Se esperan 7 columnas (Genérico, Comercial, Unidad, Costo, Ventas, Categoría, Stock).';
            $this->importing = false;
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                if (count($row) < 7 || empty(trim($row[0] ?? '')) || empty(trim($row[1] ?? ''))) {
                    $this->errorCount++;
                    continue;
                }

                // Get or create category
                $categoria_name = trim($row[5]);
                $categoria = \App\Models\Categoria::firstOrCreate(['nombre' => $categoria_name]);

                $nombre_generico = trim($row[0]);
                $nombre_comercial = trim($row[1]);
                $unidad_medida = trim($row[2]);
                $costo = (float) $row[3];
                $precio_venta = (float) $row[4];
                $stock_input = (int) $row[6];

                // Check valid unit
                if (!in_array($unidad_medida, ['Caja', 'Unidad', 'Blister', 'Frasco'])) {
                    $unidad_medida = 'Unidad'; // Default fallback
                }

                // Find or create product using medical structure
                $product = Product::updateOrCreate(
                    [
                        'nombre_comercial' => $nombre_comercial,
                        'nombre_generico' => $nombre_generico,
                    ],
                    [
                        'unidad_medida' => $unidad_medida,
                        'costo' => $costo,
                        'precio_venta' => $precio_venta,
                        'categoria_id' => $categoria->id
                    ]
                );

                // Update stock in branch
                $existingStock = $product->sucursales()
                    ->where('branch_id', $this->branch_id)
                    ->first();

                if ($existingStock) {
                    // Accumulate stock or replace? 
                    // The user said "ingresar inventario", often implies adding to existing.
                    // But massive imports often replace. Let's assume replacement for bulk sync, 
                    // or maybe we should accumulate. Let's do REPLACEMENT to match the CSV state.
                    $product->sucursales()->updateExistingPivot($this->branch_id, [
                        'stock' => $stock_input
                    ]);
                } else {
                    $product->sucursales()->attach($this->branch_id, [
                        'stock' => $stock_input
                    ]);
                }

                $this->successCount++;
            }

            DB::commit();
            $this->importMessage = "Importación completada. Éxitos: {$this->successCount}, Errores: {$this->errorCount}.";
            $this->file = null;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->importMessage = 'Error durante la importación: ' . $e->getMessage();
        }

        $this->importing = false;

        $this->dispatch('swal', [
            'title' => $this->successCount > 0 ? '¡Éxito!' : 'Aviso',
            'text' => $this->importMessage,
            'icon' => $this->successCount > 0 ? 'success' : 'info'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.inventory-import', [
            'sucursales' => Sucursal::all()
        ])->layout('layouts.plantilla');
    }
}
