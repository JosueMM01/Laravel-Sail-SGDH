<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Proveedor extends Model
{
    use Auditable;
    protected $table = 'proveedores';
    protected $fillable = ['no_proveedor', 'rfc', 'razon_social', 'direccion', 'telefono', 'correo', 'pagina_web', 'representante', 'estatus', 'last_modified_by_user_id'];

    public function lotes() { return $this->hasMany(Lote::class); }
}