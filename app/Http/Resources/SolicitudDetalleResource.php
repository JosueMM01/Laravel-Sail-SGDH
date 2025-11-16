<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudDetalleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'producto' => [
                'id' => $this->producto?->id,
                'clave' => $this->producto?->clave,
                'descripcion' => $this->producto?->descripcion,
                'presentacion' => $this->producto?->presentacion,
            ],
            'cantidad_solicitada' => $this->cantidad_solicitada,
        ];
    }
}
