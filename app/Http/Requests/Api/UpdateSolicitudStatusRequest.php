<?php

namespace App\Http\Requests\Api;

use App\Enums\SolicitudStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSolicitudStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estatus' => ['required', Rule::in(SolicitudStatus::values())],
            'motivo_rechazo' => ['nullable', 'required_if:estatus,' . SolicitudStatus::RECHAZADA->value, 'string', 'max:255'],
        ];
    }
}
