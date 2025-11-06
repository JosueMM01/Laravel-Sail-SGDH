<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaDetalle extends Model
{
    public $timestamps = false;
    protected $fillable = ['entrega_id', 'lote_id', 'cantidad_entregada'];

    public function entrega() { return $this->belongsTo(Entrega::class); }
    public function lote() { return $this->belongsTo(Lote::class); }
}
