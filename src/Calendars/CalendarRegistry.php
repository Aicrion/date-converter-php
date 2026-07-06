<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Calendars;

use Aicrion\DateConverter\Contracts\CalendarInterface;
use Aicrion\DateConverter\Enum\CalendarType;

/**
 * Resolves and caches (memoizes) calendar implementations by type,
 * avoiding repeated instantiation on every conversion call.
 */
final class CalendarRegistry
{
    /** @var array<string, CalendarInterface> */
    private array $instances = [];

    public function resolve(CalendarType $type): CalendarInterface
    {
        return $this->instances[$type->value] ??= match ($type) {
            CalendarType::Gregorian => new GregorianCalendar(),
            CalendarType::Jalali => new JalaliCalendar(),
            CalendarType::Hijri => new HijriCalendar(),
        };
    }
}
