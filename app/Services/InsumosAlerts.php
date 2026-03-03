<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InsumosAlerts
{
    /**
     * Get products with low stock (e.g., less than 10 units in a specific batch/branch)
     */
    public function getLowStock($threshold = 10)
    {
        return DB::table('product_branch')
            ->join('products', 'product_branch.product_id', '=', 'products.id')
            ->join('sucursales', 'product_branch.branch_id', '=', 'sucursales.id')
            ->where('product_branch.stock', '<=', $threshold)
            ->where('product_branch.stock', '>', 0)
            ->select(
                'products.nombre_comercial',
                'products.nombre_generico',
                'product_branch.lote',
                'product_branch.stock',
                'sucursales.nombre as sucursal'
            )
            ->get();
    }

    /**
     * Get products expiring in the next X days
     */
    public function getExpiringProducts($days)
    {
        $targetDate = Carbon::now()->addDays($days);

        return DB::table('product_branch')
            ->join('products', 'product_branch.product_id', '=', 'products.id')
            ->join('sucursales', 'product_branch.branch_id', '=', 'sucursales.id')
            ->where('product_branch.fecha_vencimiento', '<=', $targetDate)
            ->where('product_branch.fecha_vencimiento', '>', Carbon::now())
            ->where('product_branch.stock', '>', 0)
            ->select(
                'products.nombre_comercial',
                'products.nombre_generico',
                'product_branch.lote',
                'product_branch.fecha_vencimiento',
                'product_branch.stock',
                'sucursales.nombre as sucursal'
            )
            ->orderBy('product_branch.fecha_vencimiento', 'asc')
            ->get();
    }

    /**
     * Summary of alerts for a dashboard widget
     */
    public function getAlertsSummary()
    {
        return [
            'vencimiento_30' => $this->getExpiringProducts(30)->count(),
            'vencimiento_60' => $this->getExpiringProducts(60)->count(),
            'vencimiento_90' => $this->getExpiringProducts(90)->count(),
            'stock_bajo' => $this->getLowStock()->count(),
        ];
    }
}
