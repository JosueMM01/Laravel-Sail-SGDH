<?php

namespace App\Http\Resources;

use App\Enums\SolicitudStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status() ?? SolicitudStatus::PENDIENTE_JEFE;

        return [
            'id' => $this->id,
            'estatus' => [
                'value' => $status->value,
                'label' => $status->label(),
            ],
            'justificacion' => $this->justificacion,
            'fecha_solicitud' => $this->fecha_solicitud?->toIso8601String(),
            'area' => [
                'id' => $this->area?->id,
                'nombre' => $this->area?->nombre,
            ],
            'usuario_solicitante' => [
                'id' => $this->usuarioSolicitante?->id,
                'nombre' => $this->usuarioSolicitante?->name,
                'email' => $this->usuarioSolicitante?->email,
            ],
            'detalles' => SolicitudDetalleResource::collection($this->whenLoaded('detalles')),
            'actualizada_en' => $this->updated_at?->toIso8601String(),
        ];
    }
}
