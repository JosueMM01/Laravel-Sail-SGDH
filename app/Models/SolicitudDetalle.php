<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudDetalle extends Model
{
    public $timestamps = false;
    protected $fillable = ['solicitud_id', 'producto_id', 'cantidad_solicitada'];

    public function solicitud() { return $this->belongsTo(Solicitud::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}
