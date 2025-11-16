<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN_FARMACIA = 'admin_farmacia';
    case JEFE_AREA = 'jefe_area';
    case PERSONAL_AREA = 'personal_area';

    public static function values(): array
    {
        return array_map(static fn(self $role) => $role->value, self::cases());
    }

    public static function forSelection(): array
    {
        return [
            self::ADMIN_FARMACIA->value => __('Administrador de farmacia'),
            self::JEFE_AREA->value => __('Jefe de área'),
            self::PERSONAL_AREA->value => __('Personal de área'),
        ];
    }

    public static function fromMixed(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = Str::slug(str_replace(['_', '-'], ' ', strtolower($value)));

        return match ($normalized) {
            'super-admin' => self::SUPER_ADMIN,
            'administrador', 'admin', 'admin-farmacia' => self::ADMIN_FARMACIA,
            'jefe-area', 'jefe-de-area' => self::JEFE_AREA,
            'personal-area', 'personal' => self::PERSONAL_AREA,
            default => self::tryFrom($value),
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => __('Super administrador'),
            self::ADMIN_FARMACIA => __('Administrador de farmacia'),
            self::JEFE_AREA => __('Jefe de área'),
            self::PERSONAL_AREA => __('Personal de área'),
        };
    }

    public function isAssignableFromPanel(): bool
    {
        return $this !== self::SUPER_ADMIN;
    }
}
