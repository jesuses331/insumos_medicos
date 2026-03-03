<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Sale;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TableroController extends Controller
{
    /**
     * Mostrar el tablero principal.
     */
    public function inicio()
    {
        $activeBranchId = session('active_sucursal_id');
        $isGlobal = auth()->user()->can('ver-reportes-globales');

        $query = Sale::query();
        if (!$isGlobal) {
            $query->where('branch_id', $activeBranchId);
        }

        $ventasHoy = (clone $query)->whereDate('fecha', Carbon::today())->sum('total');
        $ventasMes = (clone $query)->whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->sum('total');

        // Mantengo totalUsuarios para el tercer cuadro o similar si se desea, 
        // pero la petición dice cambiar Usuarios por Ventas. 
        // Cambiaremos Usuarios -> Hoy, Roles -> Mes, Permisos -> Productos (o similar)
        $totalProductos = \App\Models\Product::count();

        // Datos para gráficos
        $topScreens = $this->getTopScreens($isGlobal, $activeBranchId);
        $monthlyRevenue = $this->getMonthlyRevenue($isGlobal, $activeBranchId);

        // Productos con bajo stock (< 5)
        $lowStockProducts = \App\Models\Product::with([
            'sucursales' => function ($q) use ($activeBranchId, $isGlobal) {
                if (!$isGlobal) {
                    $q->where('branch_id', $activeBranchId);
                }
            }
        ])
            ->whereHas('sucursales', function ($q) use ($activeBranchId, $isGlobal) {
                if (!$isGlobal) {
                    $q->where('branch_id', $activeBranchId);
                }
                $q->where('stock', '<', 5);
            })
            ->take(10)
            ->get();

        return view('admin.tablero', compact(
            'ventasHoy',
            'ventasMes',
            'totalProductos',
            'topScreens',
            'monthlyRevenue',
            'lowStockProducts'
        ));
    }

    /**
     * Obtener las 10 pantallas más vendidas (Pie Chart)
     */
    private function getTopScreens($isGlobal, $activeBranchId)
    {
        $query = Sale::query();
        if (!$isGlobal) {
            $query->where('branch_id', $activeBranchId);
        }

        $topScreens = $query->whereYear('fecha', now()->year)
            ->with(['details.product'])
            ->get()
            ->flatMap(function ($sale) {
                return $sale->details->map(function ($detail) {
                    return [
                        'product' => $detail->product,
                        'cantidad' => $detail->cantidad,
                    ];
                });
            })
            ->groupBy('product.id')
            ->map(function ($items) {
                $firstItem = $items->first();
                return [
                    'name' => $firstItem['product']->modelo ?? $firstItem['product']->marca,
                    'quantity' => $items->sum('cantidad'),
                ];
            })
            ->sortByDesc('quantity')
            ->take(10);

        return [
            'labels' => $topScreens->pluck('name')->toArray(),
            'data' => $topScreens->pluck('quantity')->toArray(),
        ];
    }

    /**
     * Obtener ventas mensuales del año actual (Line Chart)
     */
    private function getMonthlyRevenue($isGlobal, $activeBranchId)
    {
        $monthNames = [
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'
        ];

        $currentYear = now()->year;
        $monthlyData = [];

        // Inicializar todos los meses en 0
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[$month] = 0;
        }

        // Obtener ventas del año actual agrupadas por mes
        $query = Sale::query();
        if (!$isGlobal) {
            $query->where('branch_id', $activeBranchId);
        }

        $sales = $query->whereYear('fecha', $currentYear)
            ->selectRaw('MONTH(fecha) as month, SUM(total) as total')
            ->groupBy(DB::raw('MONTH(fecha)'))
            ->get();

        // Llenar los datos de meses con ventas
        foreach ($sales as $sale) {
            $monthlyData[$sale->month] = $sale->total;
        }

        return [
            'labels' => $monthNames,
            'data' => array_values($monthlyData),
        ];
    }
}
