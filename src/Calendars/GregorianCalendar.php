<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Calendars;

use Aicrion\DateConverter\Contracts\CalendarInterface;
use Aicrion\DateConverter\Enum\CalendarType;

/**
 * Gregorian (Western/civil) calendar implementation.
 *
 * Conversion to/from Julian Day Number uses the well-known
 * proleptic Gregorian algorithm (Fliegel & Van Flandern).
 */
final class GregorianCalendar implements CalendarInterface
{
    private const array MONTH_NAMES = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    private const array WEEK_DAY_NAMES = [
        0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
        4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday',
    ];

    private const array DAYS_IN_MONTH = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    public function type(): CalendarType
    {
        return CalendarType::Gregorian;
    }

    public function toJdn(int $year, int $month, int $day): int
    {
        $a = intdiv(14 - $month, 12);
        $y = $year + 4800 - $a;
        $m = $month + 12 * $a - 3;

        return $day
            + intdiv(153 * $m + 2, 5)
            + 365 * $y
            + intdiv($y, 4)
            - intdiv($y, 100)
            + intdiv($y, 400)
            - 32045;
    }

    public function fromJdn(int $jdn): array
    {
        $a = $jdn + 32044;
        $b = intdiv(4 * $a + 3, 146097);
        $c = $a - intdiv(146097 * $b, 4);
        $d = intdiv(4 * $c + 3, 1461);
        $e = $c - intdiv(1461 * $d, 4);
        $m = intdiv(5 * $e + 2, 153);

        $day = $e - intdiv(153 * $m + 2, 5) + 1;
        $month = $m + 3 - 12 * intdiv($m, 10);
        $year = 100 * $b + $d - 4800 + intdiv($m, 10);

        return [$year, $month, $day];
    }

    public function isValidDate(int $year, int $month, int $day): bool
    {
        if ($year < CalendarType::Gregorian->minYear() || $year > CalendarType::Gregorian->maxYear()) {
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
        return ($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0;
    }

    public function daysInMonth(int $year, int $month): int
    {
        if ($month === 2 && $this->isLeapYear($year)) {
            return 29;
        }

        return self::DAYS_IN_MONTH[$month - 1] ?? 30;
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
