<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sucursal_id',
        'caja_id',
        'fecha_apertura',
        'monto_apertura',
        'fecha_cierre',
        'monto_cierre',
        'total_ventas',
        'status',
        'observaciones',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
