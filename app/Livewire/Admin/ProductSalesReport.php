<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Sucursal;
use Carbon\Carbon;

class ProductSalesReport extends Component
{
    public $search = '';
    public $sortField = 'total_vendido';
    public $sortDirection = 'desc';

    // Filtros
    public $from_date = '';
    public $to_date = '';
    public $branch_id = '';

    // Datos
    public $products = [];
    public $branches = [];

    public function mount()
    {
        $this->validateBranch();
        $this->branches = Sucursal::all();

        // Inicializar fechas por defecto (último 30 días)
        $this->to_date = now()->format('Y-m-d');
        $this->from_date = now()->subDays(30)->format('Y-m-d');

        $this->loadProductSalesReport();
    }

    public function validateBranch()
    {
        if (!session('active_sucursal_id')) {
            redirect()->route('admin.sucursales.seleccionar');
        }
    }

    public function loadProductSalesReport()
    {
        // Obtener todos los detalles de ventas en el rango de fecha
        $saleDetailsQuery = SaleDetail::query()
            ->whereHas('sale', function ($query) {
                if (auth()->user()->can('ver-reportes-globales')) {
                    $query->withoutGlobalScope('branch');
                }

                $query->byDateRange($this->from_date, $this->to_date);

                if (!auth()->user()->can('ver-reportes-globales')) {
                    $query->where('user_id', auth()->id());
                    $query->where('branch_id', session('active_sucursal_id'));
                } elseif ($this->branch_id) {
                    $query->where('branch_id', $this->branch_id);
                }
            })
            ->with(['product.sucursales', 'product.marca', 'product.categoria', 'product.tipoRepuesto'])
            ->get();

        // Agrupar por producto
        $productData = $saleDetailsQuery->groupBy('product_id')->map(function ($details) {
            $product = $details->first()->product;
            $totalVendido = $details->sum('cantidad');

            // Solo incluir productos con ventas > 0
            if ($totalVendido <= 0) {
                return null;
            }

            // Obtener stock actual de la sucursal activa
            $branchId = $this->branch_id ?: session('active_sucursal_id');
            $branch = $product->sucursales->where('id', $branchId)->first();
            $stockActual = $branch ? $branch->pivot->stock : 0;

            return [
                'id' => $product->id,
                'marca' => $product->marca->nombre,
                'modelo' => $product->modelo,
                'tipo' => $product->tipoRepuesto->nombre ?? '',
                'categoria' => $product->categoria->nombre,
                'total_vendido' => (int) $totalVendido,
                'stock_actual' => (int) $stockActual,
                'reposicion' => (int) $totalVendido,
            ];
        })->filter() // Elimina valores null
            ->values() // Re-indexa el array
            ->toArray();

        $this->products = $productData;

        // Aplicar búsqueda
        if ($this->search) {
            $this->products = array_filter($this->products, function ($product) {
                $searchLower = strtolower($this->search);
                return str_contains(strtolower($product['marca']), $searchLower) ||
                    str_contains(strtolower($product['modelo']), $searchLower) ||
                    str_contains(strtolower($product['tipo']), $searchLower);
            });
            $this->products = array_values($this->products); // Re-indexa
        }

        // Aplicar ordenamiento
        $this->products = $this->sortProducts();
    }

    public function updatedFromDate()
    {
        $this->loadProductSalesReport();
    }

    public function updatedToDate()
    {
        $this->loadProductSalesReport();
    }

    public function updatedBranchId()
    {
        $this->loadProductSalesReport();
    }

    public function updatedSearch()
    {
        $this->loadProductSalesReport();
    }

    public function setSortField($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->products = $this->sortProducts();
    }

    private function sortProducts()
    {
        $products = collect($this->products);

        $sorted = $products->sortBy(function ($product) {
            return $product[$this->sortField];
        });

        if ($this->sortDirection === 'desc') {
            $sorted = $sorted->reverse();
        }

        return $sorted->toArray();
    }

    public function getSummary()
    {
        if (empty($this->products)) {
            return [
                'total_productos' => 0,
                'total_vendido' => 0,
                'reposicion_total' => 0,
            ];
        }

        $products = collect($this->products);

        return [
            'total_productos' => count($this->products),
            'total_vendido' => $products->sum('total_vendido'),
            'reposicion_total' => $products->sum('reposicion'),
        ];
    }

    public function printReport()
    {
        try {
            $summary = $this->getSummary();

            $data = [
                'products' => $this->products,
                'summary' => $summary,
                'fromDate' => $this->from_date,
                'toDate' => $this->to_date,
                'generatedAt' => now(),
            ];

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.reports.product-sales-pdf', $data);
                return $pdf->download('reporte_ventas_productos_' . now()->format('Y-m-d_H-i-s') . '.pdf');
            } else {
                return response()->json([
                    'message' => 'PDF library not installed.',
                    'data' => $data,
                    'status' => 'pending_dompdf'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function render()
    {
        return view('livewire.admin.product-sales-report')->layout('layouts.plantilla');
    }
}
