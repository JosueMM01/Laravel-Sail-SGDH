<?php

namespace App\Events;

use App\Enums\SolicitudStatus;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SolicitudStatusChanged
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Solicitud $solicitud,
        public SolicitudStatus $status,
        public ?User $performedBy = null,
        public ?string $reason = null
    ) {
    }
}
