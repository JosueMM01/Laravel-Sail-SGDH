<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Area extends Model
{
    use Auditable; // Activamos auditoría automática

    protected $fillable = ['nombre', 'responsable', 'last_modified_by_user_id'];

    public function users() { return $this->hasMany(User::class); }
    public function dotaciones() { return $this->hasMany(Dotacion::class); }
    public function solicitudes() { return $this->hasMany(Solicitud::class); }
    public function entregas() { return $this->hasMany(Entrega::class); }
}
