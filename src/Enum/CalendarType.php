<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Enum;

/**
 * Supported calendar systems.
 */
enum CalendarType: string
{
    case Gregorian = 'gregorian';
    case Jalali = 'jalali';
    case Hijri = 'hijri';

    /**
     * Human readable label (English).
     */
    public function label(): string
    {
        return match ($this) {
            self::Gregorian => 'Gregorian',
            self::Jalali => 'Jalali (Persian/Shamsi)',
            self::Hijri => 'Hijri (Islamic/Qamari)',
        };
    }

    /**
     * Minimum supported year for this calendar (safe algorithmic bounds).
     */
    public function minYear(): int
    {
        return match ($this) {
            self::Gregorian => 1,
            self::Jalali => -1096,
            self::Hijri => 1,
        };
    }

    /**
     * Maximum supported year for this calendar (safe algorithmic bounds).
     */
    public function maxYear(): int
    {
        return match ($this) {
            self::Gregorian => 3000,
            self::Jalali => 3177,
            self::Hijri => 5000,
        };
    }
}
