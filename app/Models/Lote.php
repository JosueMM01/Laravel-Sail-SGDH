<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Lote extends Model
{
    use Auditable;
    protected $fillable = ['producto_id', 'proveedor_id', 'numero_lote', 'fecha_caducidad', 'cantidad_recibida', 'cantidad_actual', 'fecha_compra', 'last_modified_by_user_id'];
    protected $casts = ['fecha_caducidad' => 'date', 'fecha_compra' => 'date'];

    public function producto() { return $this->belongsTo(Producto::class); }
    public function proveedor() { return $this->belongsTo(Proveedor::class); }
    // Scope para buscar lotes vigentes y con stock (FIFO)
    public function scopeDisponibles($query) {
         return $query->where('cantidad_actual', '>', 0)
                      ->where('fecha_caducidad', '>', now())
                      ->orderBy('fecha_caducidad', 'asc');
    }
}