<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\Auditable;

class Producto extends Model
{
    use Auditable;
    protected $fillable = ['clave', 'descripcion', 'presentacion', 'image_path', 'cuadro_basico', 'is_active', 'stock_min', 'stock_max', 'stock_optimo', 'last_modified_by_user_id'];
    protected $appends = ['image_url'];
    protected $casts = ['is_active' => 'boolean'];

    public function lotes() { return $this->hasMany(Lote::class); }
    public function dotaciones() { return $this->hasMany(Dotacion::class); }
    // Ayuda para calcular stock total rápido
    public function getStockTotalAttribute() {
        return $this->lotes()->where('fecha_caducidad', '>', now())->sum('cantidad_actual');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }
}
