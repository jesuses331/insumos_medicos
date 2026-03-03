<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'marca_id',
        'categoria_id',
        'tipo_id',
        'nombre_generico',
        'nombre_comercial',
        'costo',
        'precio_venta',
        'unidad_medida',
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function tipoRepuesto()
    {
        return $this->belongsTo(TipoRepuesto::class, 'tipo_id');
    }

    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'product_branch', 'product_id', 'branch_id')
            ->withPivot('stock', 'lote', 'fecha_vencimiento')
            ->withTimestamps();
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function transferDetails()
    {
        return $this->hasMany(TransferDetail::class);
    }
}
