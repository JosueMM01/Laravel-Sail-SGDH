<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DotacionProductoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $producto = $this->producto;

        return [
            'producto' => [
                'id' => $producto?->id,
                'clave' => $producto?->clave,
                'descripcion' => $producto?->descripcion,
                'presentacion' => $producto?->presentacion,
                'cuadro_basico' => (bool) ($producto->cuadro_basico ?? false),
                'image_url' => $producto?->image_url,
                'stock_disponible' => (int) ($producto->stock_disponible ?? 0),
            ],
            'dotacion' => [
                'cantidad_diaria' => (int) $this->cantidad_diaria,
            ],
        ];
    }
}
