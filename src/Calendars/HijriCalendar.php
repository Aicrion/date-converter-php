<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Calendars;

use Aicrion\DateConverter\Contracts\CalendarInterface;
use Aicrion\DateConverter\Enum\CalendarType;

/**
 * Hijri (Islamic / Qamari) calendar implementation.
 *
 * Uses the tabular (civil / "Kuwaiti-algorithm" adjacent) Islamic
 * calendar, the standard algorithmic approximation used across most
 * software systems in absence of local moon-sighting data. Epoch is
 * the Julian Day Number of 1 Muharram 1 AH (July 16, 622 CE, Julian
 * proleptic reckoning used by the tabular calendar).
 */
final class HijriCalendar implements CalendarInterface
{
    private const int EPOCH = 1948440;

    private const array MONTH_NAMES = [
        1 => 'محرم', 2 => 'صفر', 3 => 'ربیع‌الاول', 4 => 'ربیع‌الثانی',
        5 => 'جمادی‌الاول', 6 => 'جمادی‌الثانی', 7 => 'رجب', 8 => 'شعبان',
        9 => 'رمضان', 10 => 'شوال', 11 => 'ذیقعده', 12 => 'ذیحجه',
    ];

    private const array WEEK_DAY_NAMES = [
        0 => 'الأحد', 1 => 'الإثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء',
        4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت',
    ];

    public function type(): CalendarType
    {
        return CalendarType::Hijri;
    }

    public function toJdn(int $year, int $month, int $day): int
    {
        return $day
            + (int) ceil(29.5 * ($month - 1))
            + ($year - 1) * 354
            + intdiv(3 + 11 * $year, 30)
            + self::EPOCH
            - 1;
    }

    public function fromJdn(int $jdn): array
    {
        $year = intdiv(30 * ($jdn - self::EPOCH) + 10646, 10631);
        $month = min(12, (int) ceil(($jdn - (29 + $this->toJdn($year, 1, 1))) / 29.5) + 1);
        $day = $jdn - $this->toJdn($year, $month, 1) + 1;

        return [$year, $month, $day];
    }

    public function isValidDate(int $year, int $month, int $day): bool
    {
        if ($year < CalendarType::Hijri->minYear() || $year > CalendarType::Hijri->maxYear()) {
            return false;
        }

        if ($month < 1 || $month > 12) {
            return false;
        }

        if ($day < 1) {
            return false;
        }

        return $day <= $this->daysInMonth($year, $month);
    }

    public function isLeapYear(int $year): bool
    {
        return (11 * $year + 14) % 30 < 11;
    }

    public function daysInMonth(int $year, int $month): int
    {
        if ($month % 2 === 1) {
            return 30;
        }

        if ($month === 12 && $this->isLeapYear($year)) {
            return 30;
        }

        return 29;
    }

    public function monthsInYear(): int
    {
        return 12;
    }

    public function monthNames(): array
    {
        return self::MONTH_NAMES;
    }

    public function weekDayNames(): array
    {
        return self::WEEK_DAY_NAMES;
    }
}
