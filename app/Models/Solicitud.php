<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Solicitud extends Model
{
    use Auditable;
    protected $table = 'solicitudes';
    protected $fillable = ['area_id', 'usuario_solicitante_id', 'fecha_solicitud', 'justificacion', 'estatus', 'last_modified_by_user_id'];
    protected $casts = ['fecha_solicitud' => 'datetime'];

    public function area() { return $this->belongsTo(Area::class); }
    public function usuarioSolicitante() { return $this->belongsTo(User::class, 'usuario_solicitante_id'); }
    public function detalles() { return $this->hasMany(SolicitudDetalle::class); }
    public function entrega() { return $this->hasOne(Entrega::class); }
}