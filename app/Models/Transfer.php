<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_branch_id',
        'to_branch_id',
        'user_id',
        'status',
        'fecha',
    ];

    public function fromSucursal()
    {
        return $this->belongsTo(Sucursal::class, 'from_branch_id');
    }

    public function toSucursal()
    {
        return $this->belongsTo(Sucursal::class, 'to_branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransferDetail::class);
    }

    /**
     * Scope para filtrar traslados por rango de fechas
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
     * Scope para filtrar traslados por sucursal origen
     */
    public function scopeFromBranch($query, $branchId)
    {
        if ($branchId) {
            return $query->where('from_branch_id', $branchId);
        }
        return $query;
    }

    /**
     * Scope para filtrar traslados por sucursal destino
     */
    public function scopeToBranch($query, $branchId)
    {
        if ($branchId) {
            return $query->where('to_branch_id', $branchId);
        }
        return $query;
    }

    /**
     * Scope para filtrar traslados por estado
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope para obtener traslados con relaciones
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['fromSucursal', 'toSucursal', 'user', 'details.product']);
    }
}
