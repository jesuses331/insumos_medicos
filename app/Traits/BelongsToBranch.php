<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Sucursal;

trait BelongsToBranch
{
    protected static function bootBelongsToBranch()
    {
        static::creating(function ($model) {
            if (session()->has('active_sucursal_id') && !$model->branch_id) {
                $model->branch_id = session('active_sucursal_id');
            }
        });

        static::addGlobalScope('branch', function (Builder $builder) {
            if (session()->has('active_sucursal_id')) {
                $builder->where('branch_id', session('active_sucursal_id'));
            }
        });
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'branch_id');
    }
}
