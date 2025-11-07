<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dotacion extends Model
{
    protected $table = 'dotaciones';
    protected $fillable = ['area_id', 'producto_id', 'cantidad_diaria'];

    public function area() { return $this->belongsTo(Area::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}