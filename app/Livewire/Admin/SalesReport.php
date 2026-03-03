<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Sale;
use App\Models\Sucursal;
use Carbon\Carbon;

class SalesReport extends Component
{
    public $search = '';
    public $perPage = 10;
    public $sortField = 'fecha';
    public $sortDirection = 'desc';

    // Filtros de Ventas
    public $sales_from_date = '';
    public $sales_to_date = '';
    public $sales_branch = '';
    public $sales_category = '';

    // Datos
    public $sales = [];
    public $branches = [];

    public function mount()
    {
        $this->validateBranch();
        $this->branches = Sucursal::all();

        // Inicializar fechas por defecto (último 30 días)
        $this->sales_to_date = now()->format('Y-m-d');
        $this->sales_from_date = now()->subDays(30)->format('Y-m-d');

        $this->loadSalesReport();
    }

    public function validateBranch()
    {
        if (!session('active_sucursal_id')) {
            redirect()->route('admin.sucursales.seleccionar');
        }
    }

    #[On('loadSalesReport')]
    public function loadSalesReport()
    {
        $query = Sale::query();

        // Bypass global branch scope for admins
        if (auth()->user()->can('ver-reportes-globales')) {
            $query->withoutGlobalScope('branch');
        }

        $query->byDateRange($this->sales_from_date, $this->sales_to_date)
            ->withSummary();

        // Restringir a sus propias ventas si no es Admin/Dueño
        if (!auth()->user()->can('ver-reportes-globales')) {
            $query->where('user_id', auth()->id());
            $query->where('branch_id', session('active_sucursal_id'));
        } elseif ($this->sales_branch) {
            $query->where('branch_id', $this->sales_branch);
        }

        if ($this->sales_category) {
            $query->byCategory($this->sales_category);
        }

        // Aplicar búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                    ->orWhere('client_name', 'like', '%' . $this->search . '%')
                    ->orWhere('fecha', 'like', '%' . $this->search . '%');
            });
        }

        $this->sales = $query->orderBy($this->sortField, $this->sortDirection)->get()->toArray();
    }

    public function updatedSalesFromDate()
    {
        $this->loadSalesReport();
    }

    public function updatedSalesToDate()
    {
        $this->loadSalesReport();
    }

    public function updatedSalesBranch()
    {
        $this->loadSalesReport();
    }

    public function updatedSalesCategory()
    {
        $this->loadSalesReport();
    }

    public function updatedSearch()
    {
        $this->loadSalesReport();
    }

    public function setSortField($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadSalesReport();
    }

    /**
     * Generar PDF de reporte de ventas
     */
    public function printReport()
    {
        try {
            // Obtener datos filtrados
            $query = Sale::query();

            if (auth()->user()->can('ver-reportes-globales')) {
                $query->withoutGlobalScope('branch');
            }

            $query->byDateRange($this->sales_from_date, $this->sales_to_date)
                ->withSummary();

            if (!auth()->user()->can('ver-reportes-globales')) {
                $query->where('user_id', auth()->id());
                $query->where('branch_id', session('active_sucursal_id'));
            } elseif ($this->sales_branch) {
                $query->where('branch_id', $this->sales_branch);
            }

            if ($this->sales_category) {
                $query->byCategory($this->sales_category);
            }

            $salesData = $query->orderBy('fecha', 'desc')->get();

            // Calcular resumen con validaciones
            $totalSold = $salesData->sum('total');
            $estimatedProfit = $salesData->sum(function ($sale) {
                return $sale->details->sum(function ($detail) {
                    $costo = $detail->product->costo ?? 0;
                    $cost = $costo * $detail->cantidad;
                    $revenue = $detail->precio_unitario * $detail->cantidad;
                    return $revenue - $cost;
                });
            });

            $topProduct = $this->getTopProduct($salesData);
            $activeBranch = $this->sales_branch
                ? Sucursal::find($this->sales_branch)
                : Sucursal::find(session('active_sucursal_id'));

            $data = [
                'sales' => $salesData,
                'totalSold' => $totalSold,
                'estimatedProfit' => $estimatedProfit,
                'topProduct' => $topProduct,
                'branch' => $activeBranch,
                'fromDate' => $this->sales_from_date,
                'toDate' => $this->sales_to_date,
                'generatedAt' => now(),
            ];

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.reports.sales-pdf', $data);
                return $pdf->download('reporte_ventas_' . now()->format('Y-m-d_H-i-s') . '.pdf');
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

    /**
     * Obtener producto más vendido
     */
    private function getTopProduct($sales)
    {
        $productSales = [];

        foreach ($sales as $sale) {
            foreach ($sale->details as $detail) {
                if (!$detail->product)
                    continue;
                $productId = $detail->product->id;
                if (!isset($productSales[$productId])) {
                    $productSales[$productId] = [
                        'product' => $detail->product,
                        'quantity' => 0,
                        'revenue' => 0,
                    ];
                }
                $productSales[$productId]['quantity'] += $detail->cantidad;
                $productSales[$productId]['revenue'] += $detail->precio_unitario * $detail->cantidad;
            }
        }

        if (empty($productSales)) {
            return null;
        }

        return collect($productSales)
            ->sortByDesc('quantity')
            ->first();
    }

    /**
     * Calcular resumen de ventas
     */
    public function getSalesSummary()
    {
        if (empty($this->sales)) {
            return [
                'total' => 0,
                'profit' => 0,
                'topProduct' => null,
                'salesCount' => 0,
            ];
        }

        $sales = collect($this->sales);
        $totalSold = $sales->sum('total');

        $estimatedProfit = $sales->sum(function ($sale) {
            if (!isset($sale['details']))
                return 0;
            return collect($sale['details'])->sum(function ($detail) {
                $costo = $detail['product']['costo'] ?? 0;
                $cost = $costo * $detail['cantidad'];
                $revenue = $detail['precio_unitario'] * $detail['cantidad'];
                return $revenue - $cost;
            });
        });

        $topProduct = $this->getTopProductFromArray($sales);

        return [
            'total' => $totalSold,
            'profit' => $estimatedProfit,
            'topProduct' => $topProduct,
            'salesCount' => count($this->sales),
        ];
    }

    /**
     * Obtener producto más vendido desde array
     */
    private function getTopProductFromArray($sales)
    {
        $productSales = [];

        foreach ($sales as $sale) {
            foreach ($sale['details'] as $detail) {
                $productId = $detail['product']['id'];
                if (!isset($productSales[$productId])) {
                    $productSales[$productId] = [
                        'product' => $detail['product'],
                        'quantity' => 0,
                    ];
                }
                $productSales[$productId]['quantity'] += $detail['cantidad'];
            }
        }

        if (empty($productSales)) {
            return null;
        }

        return collect($productSales)
            ->sortByDesc('quantity')
            ->first();
    }

    public function render()
    {
        return view('livewire.admin.sales-report')->layout('layouts.plantilla');
    }
}
