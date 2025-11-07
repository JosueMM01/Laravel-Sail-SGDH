<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Producto extends Model
{
    use Auditable;
    protected $fillable = ['clave', 'descripcion', 'presentacion', 'cuadro_basico', 'stock_min', 'stock_max', 'stock_optimo', 'last_modified_by_user_id'];

    public function lotes() { return $this->hasMany(Lote::class); }
    public function dotaciones() { return $this->hasMany(Dotacion::class); }
    // Ayuda para calcular stock total rápido
    public function getStockTotalAttribute() {
        return $this->lotes()->where('fecha_caducidad', '>', now())->sum('cantidad_actual');
    }
}
