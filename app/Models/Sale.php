<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Traits\BelongsToBranch;

class Sale extends Model
{
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'branch_id',
        'user_id',
        'cash_register_id',
        'total',
        'client_name',
        'payment_method',
        'fecha',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    /**
     * Scope para filtrar ventas por rango de fechas
     */
    public function scopeByDateRange($query, $from, $to)
    {
        if ($from && $to) {
            $fromDate = Carbon::parse($from)->startOfDay();
            $toDate = Carbon::parse($to)->endOfDay();
            return $query->whereBetween('fecha', [$fromDate, $toDate]);
        }
        return $query;
    }

    /**
     * Scope para filtrar ventas por sucursal específica (sobrescribe el global scope)
     */
    public function scopeByBranch($query, $branchId)
    {
        if ($branchId) {
            return $query->where('branch_id', $branchId);
        }
        return $query;
    }

    /**
     * Scope para filtrar ventas por categoría de producto
     */
    public function scopeByCategory($query, $category)
    {
        if ($category) {
            return $query->whereHas('details.product', function ($q) use ($category) {
                $q->where('categoria', $category);
            });
        }
        return $query;
    }

    /**
     * Scope para obtener resumen de ventas (agrupación)
     */
    public function scopeWithSummary($query)
    {
        return $query->with(['details.product', 'user', 'sucursal']);
    }
}
