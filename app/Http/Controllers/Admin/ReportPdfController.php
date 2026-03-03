<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Transfer;
use App\Models\Product;
use App\Models\SaleDetail;
use App\Models\Sucursal;
use Carbon\Carbon;

class ReportPdfController extends Controller
{
    /**
     * Descargar PDF de Reporte de Ventas
     */
    public function downloadSalesPdf(Request $request)
    {
        try {
            $fromDate = $request->query('from_date');
            $toDate = $request->query('to_date');
            $branchId = $request->query('branch_id');
            $category = $request->query('category');

            $query = Sale::query();

            if (auth()->user()->can('ver-reportes-globales')) {
                $query->withoutGlobalScope('branch');
            }

            $query->byDateRange($fromDate, $toDate)
                ->with(['details.product', 'user', 'cashRegister', 'sucursal']);

            if (!auth()->user()->can('ver-reportes-globales')) {
                $query->where('user_id', auth()->id());
                $query->where('branch_id', session('active_sucursal_id'));
            } elseif ($branchId) {
                $query->where('branch_id', $branchId);
            }

            if ($category) {
                $query->byCategory($category);
            }

            $sales = $query->orderBy('fecha', 'desc')->get();

            $totalSold = $sales->sum('total');
            $estimatedProfit = $sales->sum(function ($sale) {
                return $sale->details->sum(function ($detail) {
                    $costo = $detail->product->costo ?? 0;
                    $cost = $costo * $detail->cantidad;
                    $revenue = $detail->precio_unitario * $detail->cantidad;
                    return $revenue - $cost;
                });
            });

            $data = [
                'sales' => $sales,
                'totalSold' => $totalSold,
                'estimatedProfit' => $estimatedProfit,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'generatedAt' => now(),
            ];

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.reports.sales-pdf', $data);
                $pdf->setPaper('A4', 'landscape');
                return $pdf->download('reporte_ventas_' . now()->format('Y-m-d_H-i-s') . '.pdf');
            } else {
                return response()->json(['error' => 'PDF library not installed'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Descargar PDF de Reporte de Traslados
     */
    public function downloadTransfersPdf(Request $request)
    {
        try {
            $fromDate = $request->query('from_date');
            $toDate = $request->query('to_date');
            $fromBranch = $request->query('from_branch');
            $toBranch = $request->query('to_branch');
            $status = $request->query('status');

            $query = Transfer::query()
                ->byDateRange($fromDate, $toDate)
                ->withRelations();

            if ($fromBranch) {
                $query->fromBranch($fromBranch);
            }

            if ($toBranch) {
                $query->toBranch($toBranch);
            }

            if ($status) {
                $query->byStatus($status);
            }

            $transfers = $query->orderBy('fecha', 'desc')->get();

            $totalTransfers = $transfers->count();
            $totalItems = $transfers->sum(function ($transfer) {
                return $transfer->details->sum('cantidad');
            });

            $byStatus = $transfers->groupBy('status')->map->count();

            $data = [
                'transfers' => $transfers,
                'totalTransfers' => $totalTransfers,
                'totalItems' => $totalItems,
                'byStatus' => $byStatus,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'generatedAt' => now(),
            ];

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.reports.transfers-pdf', $data);
                $pdf->setPaper('A4', 'landscape');
                return $pdf->download('reporte_traslados_' . now()->format('Y-m-d_H-i-s') . '.pdf');
            } else {
                return response()->json(['error' => 'PDF library not installed'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Descargar PDF de Reporte de Productos
     */
    public function downloadProductsPdf(Request $request)
    {
        try {
            $fromDate = $request->query('from_date');
            $toDate = $request->query('to_date');
            $branchId = $request->query('branch_id');

            $saleDetailsQuery = SaleDetail::query()
                ->whereHas('sale', function ($query) use ($fromDate, $toDate, $branchId) {
                    if (auth()->user()->can('ver-reportes-globales')) {
                        $query->withoutGlobalScope('branch');
                    }

                    $query->byDateRange($fromDate, $toDate);

                    if (!auth()->user()->can('ver-reportes-globales')) {
                        $query->where('user_id', auth()->id());
                        $query->where('branch_id', session('active_sucursal_id'));
                    } elseif ($branchId) {
                        $query->where('branch_id', $branchId);
                    }
                })
                ->with(['product.sucursales', 'product.marca', 'product.categoria', 'product.tipoRepuesto'])
                ->get();

            // Agrupar por producto
            $productData = $saleDetailsQuery->groupBy('product_id')->map(function ($details) use ($branchId) {
                $product = $details->first()->product;
                $totalVendido = $details->sum('cantidad');

                if ($totalVendido <= 0) {
                    return null;
                }

                $branchId = $branchId ?: session('active_sucursal_id');
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
            })->filter()->values()->toArray();

            $products = collect($productData);
            $summary = [
                'total_productos' => count($productData),
                'total_vendido' => $products->sum('total_vendido'),
                'reposicion_total' => $products->sum('reposicion'),
            ];

            $data = [
                'products' => $productData,
                'summary' => $summary,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'generatedAt' => now(),
            ];

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.reports.product-sales-pdf', $data);
                $pdf->setPaper('A4', 'landscape');
                return $pdf->download('reporte_productos_' . now()->format('Y-m-d_H-i-s') . '.pdf');
            } else {
                return response()->json(['error' => 'PDF library not installed'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Descargar PDF de una Cotización específica
     */
    public function downloadQuotationPdf($id)
    {
        try {
            $quotation = \App\Models\Quotation::with(['details.product', 'sucursal'])->findOrFail($id);

            $data = [
                'quotation' => $quotation,
            ];

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reportes.pdf.cotizacion', $data);
                return $pdf->download('cotizacion_' . str_pad($quotation->id, 5, '0', STR_PAD_LEFT) . '.pdf');
            } else {
                return response()->json(['error' => 'PDF library not installed'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Descargar PDF de una venta específica
     */
    public function downloadSaleDetailPdf($id)
    {
        $sale = Sale::with(['details.product', 'sucursal', 'user', 'cashRegister'])->find($id);

        if (!$sale) {
            return back()->with('error', 'Venta no encontrada');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reportes.pdf.venta_detalle', compact('sale'));

        return $pdf->download('venta_' . str_pad($sale->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }

    /**
     * Descargar PDF de Reposición Unificado (Ventas + Defectuosos)
     */
    public function downloadReplenishmentPdf(Request $request)
    {
        try {
            $fromDate = $request->query('from_date');
            $toDate = $request->query('to_date');
            $branchId = $request->query('branch_id');

            // 1. Obtener Unidades Vendidas (Ventas)
            $saleDetailsQuery = SaleDetail::query()
                ->whereHas('sale', function ($query) use ($fromDate, $toDate, $branchId) {
                    if (auth()->user()->can('ver-reportes-globales')) {
                        $query->withoutGlobalScope('branch');
                    }
                    $query->byDateRange($fromDate, $toDate);
                    if ($branchId) {
                        $query->where('branch_id', $branchId);
                    }
                })
                ->with(['product.marca', 'product.categoria', 'product.tipoRepuesto', 'sale.user', 'sale.cashRegister'])
                ->get();

            $soldProducts = $saleDetailsQuery->groupBy('product_id')->map(function ($details) {
                $product = $details->first()->product;
                return [
                    'product_id' => $product->id,
                    'name' => $product->marca->nombre . ' ' . $product->modelo . ($product->tipoRepuesto ? ' (' . $product->tipoRepuesto->nombre . ')' : ''),
                    'quantity' => $details->sum('cantidad'),
                    'category' => $product->categoria->nombre
                ];
            });

            // 2. Obtener Productos Defectuosos (NUEVO)
            $defectiveQuery = \App\Models\DefectiveProduct::query();
            if ($fromDate && $toDate) {
                $defectiveQuery->whereBetween('created_at', [Carbon::parse($fromDate)->startOfDay(), Carbon::parse($toDate)->endOfDay()]);
            }
            if ($branchId) {
                $defectiveQuery->where('sucursal_id', $branchId);
            }
            $defectiveItems = $defectiveQuery->with(['product.marca', 'product.tipoRepuesto'])->get();

            $defectiveProducts = $defectiveItems->groupBy('product_id')->map(function ($items) {
                $product = $items->first()->product;
                return [
                    'product_id' => $product->id,
                    'name' => $product->marca->nombre . ' ' . $product->modelo . ($product->tipoRepuesto ? ' (' . $product->tipoRepuesto->nombre . ')' : ''),
                    'quantity' => $items->sum('cantidad'),
                    'details' => $items->pluck('detalle')->unique()->filter()->implode(', ')
                ];
            });

            $data = [
                'soldProducts' => $soldProducts,
                'defectiveProducts' => $defectiveProducts,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'branch' => $branchId ? Sucursal::find($branchId) : null,
                'generatedAt' => now(),
            ];

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reportes.pdf.reposicion_consolidada', $data);
                return $pdf->download('reporte_reposicion_' . now()->format('Y-m-d_H-i-s') . '.pdf');
            }
            return response()->json(['error' => 'DomPDF not found'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Descargar PDF de Productos Defectuosos
     */
    public function downloadDefectivePdf(Request $request)
    {
        try {
            $defectuosos = \App\Models\DefectiveProduct::with(['product', 'sucursal', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            $data = [
                'defectuosos' => $defectuosos,
                'generatedAt' => now(),
            ];

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reportes.pdf.defectuosos', $data);
                return $pdf->download('reporte_defectuosos_' . now()->format('Y-m-d_H-i-s') . '.pdf');
            }
            return response()->json(['error' => 'DomPDF not found'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
