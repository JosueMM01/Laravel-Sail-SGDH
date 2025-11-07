<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    // No usa Auditable porque ya tiene 'usuario_entrega_id' que cumple esa función
    protected $fillable = ['tipo_entrega', 'area_id', 'usuario_entrega_id', 'fecha_entrega', 'solicitud_id'];
    protected $casts = ['fecha_entrega' => 'datetime'];

    public function area() { return $this->belongsTo(Area::class); }
    public function usuarioEntrega() { return $this->belongsTo(User::class, 'usuario_entrega_id'); }
    public function solicitud() { return $this->belongsTo(Solicitud::class); }
    public function detalles() { return $this->hasMany(EntregaDetalle::class); }
}
