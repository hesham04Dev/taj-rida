<?php

namespace App\Enums;

enum MemorizationType: string
{
    case Regular = 'regular';
    case Serd = 'serd';

    /**
     * Get the Arabic label for the type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Regular => 'حفظ عادي',
            self::Serd => 'سرد',
        };
    }

    /**
     * Get the settings key for points-per-page for this type.
     */
    public function pointsSettingKey(): string
    {
        return match ($this) {
            self::Regular => 'points_per_page_regular',
            self::Serd => 'points_per_page_serd',
        };
    }

    /**
     * Get default points per page for this type.
     */
    public function defaultPointsPerPage(): float
    {
        return match ($this) {
            self::Regular => 1.0,
            self::Serd => 0.5,
        };
    }

    /**
     * Get options array suitable for Filament Select/ToggleButtons.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->all();
    }
}
