<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRepuesto extends Model
{
    protected $table = 'tipos_repuesto';
    protected $fillable = ['nombre'];

    public function productos()
    {
        return $this->hasMany(Product::class);
    }
}
