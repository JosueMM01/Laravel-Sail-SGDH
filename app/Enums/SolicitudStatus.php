<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum SolicitudStatus: string
{
    case PENDIENTE_JEFE = 'pendiente_jefe';
    case PENDIENTE_FARMACIA = 'pendiente_farmacia';
    case APROBADA = 'aprobada';
    case RECHAZADA = 'rechazada';
    case SURTIDA = 'surtida';

    public static function values(): array
    {
        return array_map(static fn(self $status) => $status->value, self::cases());
    }

    public static function ordered(): array
    {
        return [
            self::PENDIENTE_JEFE,
            self::PENDIENTE_FARMACIA,
            self::APROBADA,
            self::SURTIDA,
            self::RECHAZADA,
        ];
    }

    public static function fromMixed(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = Str::slug(str_replace(['_', '-'], ' ', strtolower($value)));

        return match ($normalized) {
            'pendiente-jefe', 'pendiente' => self::PENDIENTE_JEFE,
            'pendiente-farmacia', 'espera-farmacia' => self::PENDIENTE_FARMACIA,
            'aprobada' => self::APROBADA,
            'rechazada' => self::RECHAZADA,
            'surtida' => self::SURTIDA,
            default => self::tryFrom($value),
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDIENTE_JEFE => __('Pendiente de jefe de área'),
            self::PENDIENTE_FARMACIA => __('Pendiente de farmacia'),
            self::APROBADA => __('Aprobada'),
            self::RECHAZADA => __('Rechazada'),
            self::SURTIDA => __('Surtida'),
        };
    }

    public function badgeClasses(): array
    {
        return match ($this) {
            self::PENDIENTE_JEFE => ['badge' => 'bg-[#fff8e6] text-[#b78a1f]', 'dot' => 'bg-[#d19b2a]'],
            self::PENDIENTE_FARMACIA => ['badge' => 'bg-[#f0f7ff] text-[#1c4ed8]', 'dot' => 'bg-[#1c4ed8]'],
            self::APROBADA => ['badge' => 'bg-[#e9f7e9] text-[#1b7a1b]', 'dot' => 'bg-[#1b7a1b]'],
            self::RECHAZADA => ['badge' => 'bg-[#ffefef] text-[#b42323]', 'dot' => 'bg-[#b42323]'],
            self::SURTIDA => ['badge' => 'bg-[#f1f5f1] text-slate-600', 'dot' => 'bg-slate-400'],
        };
    }

    public function transitions(): array
    {
        return match ($this) {
            self::PENDIENTE_JEFE => [self::PENDIENTE_FARMACIA],
            self::PENDIENTE_FARMACIA => [self::APROBADA, self::RECHAZADA],
            self::APROBADA => [self::SURTIDA, self::PENDIENTE_FARMACIA],
            self::RECHAZADA => [self::PENDIENTE_FARMACIA],
            self::SURTIDA => [],
        };
    }
}
