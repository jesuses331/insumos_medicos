<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefectiveProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'sucursal_id',
        'user_id',
        'cantidad',
        'detalle',
        'estado',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
