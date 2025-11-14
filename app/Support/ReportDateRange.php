<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use InvalidArgumentException;

class ReportDateRange
{
    public const LAST_7_DAYS = 'last_7_days';
    public const LAST_15_DAYS = 'last_15_days';
    public const LAST_30_DAYS = 'last_30_days';
    public const LAST_QUARTER = 'last_quarter';
    public const CUSTOM = 'custom';

    public function __construct(
        public readonly string $key,
        public readonly Carbon $start,
        public readonly Carbon $end
    ) {
    }

    public static function resolve(string $key, ?string $from = null, ?string $to = null): self
    {
        $end = Carbon::now()->endOfDay();

        $range = match ($key) {
            self::LAST_7_DAYS => [Carbon::now()->subDays(6)->startOfDay(), $end],
            self::LAST_15_DAYS => [Carbon::now()->subDays(14)->startOfDay(), $end],
            self::LAST_30_DAYS => [Carbon::now()->subDays(29)->startOfDay(), $end],
            self::LAST_QUARTER => [Carbon::now()->subDays(89)->startOfDay(), $end],
            self::CUSTOM => self::resolveCustomRange($from, $to),
            default => throw new InvalidArgumentException('Rango de reporte inválido.'),
        };

        return new self($key, $range[0], $range[1]);
    }

    public function label(): string
    {
        return match ($this->key) {
            self::LAST_7_DAYS => 'Últimos 7 días',
            self::LAST_15_DAYS => 'Últimos 15 días',
            self::LAST_30_DAYS => 'Último mes (30 días)',
            self::LAST_QUARTER => 'Últimos 3 meses (90 días)',
            self::CUSTOM => sprintf('Del %s al %s', $this->start->isoFormat('DD/MM/YYYY'), $this->end->isoFormat('DD/MM/YYYY')),
            default => 'Rango sin título',
        };
    }

    public static function options(): array
    {
        return [
            self::LAST_7_DAYS => 'Últimos 7 días',
            self::LAST_15_DAYS => 'Últimos 15 días',
            self::LAST_30_DAYS => 'Último mes (30 días)',
            self::LAST_QUARTER => 'Últimos 3 meses (90 días)',
            self::CUSTOM => 'Personalizado',
        ];
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label(),
            'start' => $this->start,
            'end' => $this->end,
        ];
    }

    private static function resolveCustomRange(?string $from, ?string $to): array
    {
        if (! $from || ! $to) {
            throw new InvalidArgumentException('Debes proporcionar fechas de inicio y fin para el rango personalizado.');
        }

        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->endOfDay();

        if ($start->greaterThan($end)) {
            throw new InvalidArgumentException('La fecha de inicio no puede ser posterior a la fecha fin.');
        }

        return [$start, $end];
    }
}
