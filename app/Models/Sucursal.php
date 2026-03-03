<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
    ];

    public function productos()
    {
        return $this->belongsToMany(Product::class, 'product_branch', 'branch_id', 'product_id')
            ->withPivot('stock')
            ->withTimestamps();
    }
}
