<?php

namespace App\Http\Requests;

use App\Support\ReportDateRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportDownloadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rangeKeys = array_keys(ReportDateRange::options());

        return [
            'range' => ['required', Rule::in($rangeKeys)],
            'from' => ['required_if:range,' . ReportDateRange::CUSTOM, 'nullable', 'date'],
            'to' => ['required_if:range,' . ReportDateRange::CUSTOM, 'nullable', 'date', 'after_or_equal:from'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.required_if' => 'La fecha de inicio es obligatoria cuando eliges un rango personalizado.',
            'to.required_if' => 'La fecha final es obligatoria cuando eliges un rango personalizado.',
            'to.after_or_equal' => 'La fecha final debe ser posterior o igual a la fecha de inicio.',
        ];
    }

    public function range(): ReportDateRange
    {
        return ReportDateRange::resolve(
            $this->input('range'),
            $this->input('from'),
            $this->input('to')
        );
    }
}
