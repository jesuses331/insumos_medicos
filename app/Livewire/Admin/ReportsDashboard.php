<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Sale;
use App\Models\Transfer;
use App\Models\Sucursal;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportsDashboard extends Component
{
    public $activeTab = 'sales';

    // Filtros de Ventas
    public $sales_from_date = '';
    public $sales_to_date = '';
    public $sales_branch = '';
    public $sales_category = '';

    // Filtros de Traslados
    public $transfers_from_date = '';
    public $transfers_to_date = '';
    public $transfers_from_branch = '';
    public $transfers_to_branch = '';
    public $transfers_status = '';

    // Datos
    public $sales = [];
    public $transfers = [];
    public $branches = [];

    public function mount()
    {
        $this->validateBranch();
        $this->branches = Sucursal::all();

        // Inicializar fechas por defecto (último 30 días)
        $this->sales_to_date = now()->format('Y-m-d');
        $this->sales_from_date = now()->subDays(30)->format('Y-m-d');

        $this->transfers_to_date = now()->format('Y-m-d');
        $this->transfers_from_date = now()->subDays(30)->format('Y-m-d');

        $this->loadSalesReport();
        $this->loadTransfersReport();
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

        if (auth()->user()->can('ver-reportes-globales')) {
            $query->withoutGlobalScope('branch');
        }

        $query->byDateRange($this->sales_from_date, $this->sales_to_date)
            ->withSummary();

        if (!auth()->user()->can('ver-reportes-globales')) {
            $query->where('branch_id', session('active_sucursal_id'));
        } elseif ($this->sales_branch) {
            $query->where('branch_id', $this->sales_branch);
        }

        if ($this->sales_category) {
            $query->byCategory($this->sales_category);
        }

        $this->sales = $query->orderBy('fecha', 'desc')->get()->toArray();
    }

    #[On('loadTransfersReport')]
    public function loadTransfersReport()
    {
        $query = Transfer::query()
            ->byDateRange($this->transfers_from_date, $this->transfers_to_date)
            ->withRelations();

        if ($this->transfers_from_branch) {
            $query->fromBranch($this->transfers_from_branch);
        }

        if ($this->transfers_to_branch) {
            $query->toBranch($this->transfers_to_branch);
        }

        if ($this->transfers_status) {
            $query->byStatus($this->transfers_status);
        }

        $this->transfers = $query->orderBy('fecha', 'desc')->get()->toArray();
    }

    // Métodos agregados para ir directamente cuando cambia el wire:model.live
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

    public function updatedTransfersFromDate()
    {
        $this->loadTransfersReport();
    }

    public function updatedTransfersToDate()
    {
        $this->loadTransfersReport();
    }

    public function updatedTransfersFromBranch()
    {
        $this->loadTransfersReport();
    }

    public function updatedTransfersToBranch()
    {
        $this->loadTransfersReport();
    }

    public function updatedTransfersStatus()
    {
        $this->loadTransfersReport();
    }

    /**
     * Generar PDF de reporte de ventas
     */
    public function exportSalesPDF()
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
                $query->where('branch_id', session('active_sucursal_id'));
            } elseif ($this->sales_branch) {
                $query->where('branch_id', $this->sales_branch);
            }

            if ($this->sales_category) {
                $query->byCategory($this->sales_category);
            }

            $salesData = $query->orderBy('fecha', 'desc')->get();

            // Calcular resumen
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
            $activeBranch = ($this->sales_branch || !auth()->user()->can('ver-reportes-globales'))
                ? Sucursal::find($this->sales_branch ?: session('active_sucursal_id'))
                : null;

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

            // Si está instalado DomPDF, usarlo
            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.reports.sales-pdf', $data);
                return $pdf->download('reporte_ventas_' . now()->format('Y-m-d_H-i-s') . '.pdf');
            } else {
                // Fallback: Enviar JSON con datos para que el cliente genere PDF
                return response()->json([
                    'message' => 'PDF library not installed. Generating with available data.',
                    'data' => $data,
                    'status' => 'pending_dompdf'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generar PDF de reporte de traslados
     */
    public function exportTransfersPDF()
    {
        try {
            $query = Transfer::query();

            if (auth()->user()->can('ver-reportes-globales')) {
                $query->withoutGlobalScope('branch');
            }

            $query->byDateRange($this->transfers_from_date, $this->transfers_to_date)
                ->withRelations();

            if ($this->transfers_from_branch) {
                $query->fromBranch($this->transfers_from_branch);
            }

            if ($this->transfers_to_branch) {
                $query->toBranch($this->transfers_to_branch);
            }

            if ($this->transfers_status) {
                $query->byStatus($this->transfers_status);
            }

            $transfersData = $query->orderBy('fecha', 'desc')->get();

            $data = [
                'transfers' => $transfersData,
                'fromDate' => $this->transfers_from_date,
                'toDate' => $this->transfers_to_date,
                'generatedAt' => now(),
            ];

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.reports.transfers-pdf', $data);
                return $pdf->download('reporte_traslados_' . now()->format('Y-m-d_H-i-s') . '.pdf');
            } else {
                return response()->json([
                    'message' => 'PDF library not installed. Generating with available data.',
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

        $salesData = collect($this->sales);
        $totalSold = $salesData->sum('total');

        $estimatedProfit = $salesData->sum(function ($sale) {
            if (!isset($sale['details']))
                return 0;
            return collect($sale['details'])->sum(function ($detail) {
                $costo = $detail['product']['costo'] ?? 0;
                $cost = $costo * $detail['cantidad'];
                $revenue = $detail['precio_unitario'] * $detail['cantidad'];
                return $revenue - $cost;
            });
        });

        $topProduct = $this->getTopProductFromArray($salesData);

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

    /**
     * Calcular resumen de traslados
     */
    public function getTransfersSummary()
    {
        if (empty($this->transfers)) {
            return [
                'totalTransfers' => 0,
                'totalItems' => 0,
                'bySatus' => [],
            ];
        }

        $transfers = collect($this->transfers);
        $totalItems = $transfers->sum(function ($transfer) {
            return collect($transfer['details'])->sum('cantidad');
        });

        $byStatus = $transfers->groupBy('status')->map->count();

        return [
            'totalTransfers' => count($this->transfers),
            'totalItems' => $totalItems,
            'byStatus' => $byStatus,
        ];
    }

    /**
     * Obtener datos de ventas diarias para gráfico
     */
    public function getDailySalesChartData()
    {
        $query = Sale::query();

        if (auth()->user()->can('ver-reportes-globales')) {
            $query->withoutGlobalScope('branch');
        }

        $query->byDateRange($this->sales_from_date, $this->sales_to_date)
            ->withSummary();

        if (!auth()->user()->can('ver-reportes-globales')) {
            $query->where('branch_id', session('active_sucursal_id'));
        } elseif ($this->sales_branch) {
            $query->where('branch_id', $this->sales_branch);
        }

        if ($this->sales_category) {
            $query->byCategory($this->sales_category);
        }

        $sales = $query->get();

        // Agrupar por fecha
        $salesByDate = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->fecha)->format('Y-m-d');
        })->map(function ($daySales) {
            return $daySales->sum('total');
        });

        // Crear array de fechas del rango
        $fromDate = Carbon::parse($this->sales_from_date);
        $toDate = Carbon::parse($this->sales_to_date);
        $dates = [];
        $values = [];

        while ($fromDate <= $toDate) {
            $dateStr = $fromDate->format('Y-m-d');
            $dates[] = $fromDate->format('d/m');
            $values[] = $salesByDate[$dateStr] ?? 0;
            $fromDate->addDay();
        }

        return [
            'labels' => $dates,
            'data' => $values,
        ];
    }

    /**
     * Obtener datos de modelos más vendidos para gráfico
     */
    public function getTopScreensChartData()
    {
        $query = Sale::query();

        if (auth()->user()->can('ver-reportes-globales')) {
            $query->withoutGlobalScope('branch');
        }

        $query->byDateRange($this->sales_from_date, $this->sales_to_date)
            ->withSummary();

        if (!auth()->user()->can('ver-reportes-globales')) {
            $query->where('branch_id', session('active_sucursal_id'));
        } elseif ($this->sales_branch) {
            $query->where('branch_id', $this->sales_branch);
        }

        // Filtrar solo pantallas
        $query->whereHas('details.product', function ($q) {
            $q->where('categoria', 'pantalla');
        });

        $sales = $query->get();

        // Agrupar por modelo
        $modelSales = [];
        foreach ($sales as $sale) {
            foreach ($sale->details as $detail) {
                $product = $detail->product;
                // Usar modelo como clave (si existe), si no usar marca
                $modelKey = $product->modelo ?? $product->marca;

                if (!isset($modelSales[$modelKey])) {
                    $modelSales[$modelKey] = [
                        'quantity' => 0,
                        'revenue' => 0,
                    ];
                }
                $modelSales[$modelKey]['quantity'] += $detail->cantidad;
                $modelSales[$modelKey]['revenue'] += $detail->precio_unitario * $detail->cantidad;
            }
        }

        // Ordenar por cantidad y tomar top 10
        $topScreens = collect($modelSales)
            ->sortByDesc('quantity')
            ->take(10);

        $labels = $topScreens->keys()->toArray();
        $values = $topScreens->pluck('quantity')->toArray();
        $revenues = $topScreens->pluck('revenue')->toArray();

        return [
            'labels' => $labels,
            'data' => $values,
            'revenues' => $revenues,
        ];
    }

    public function render()
    {
        return view('livewire.admin.reports-dashboard')->layout('layouts.plantilla');
    }
}
