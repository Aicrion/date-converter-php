<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Contracts;

use Aicrion\DateConverter\Enum\CalendarType;

/**
 * Contract implemented by every calendar system supported by the library.
 *
 * Every implementation must be able to convert its own year/month/day
 * representation to a Julian Day Number (the universal pivot used
 * internally for all cross-calendar conversions) and back, as well as
 * validate and describe itself (days in month, leap years, etc.).
 */
interface CalendarInterface
{
    public function type(): CalendarType;

    /**
     * Converts a year/month/day triple of this calendar into a Julian Day Number.
     */
    public function toJdn(int $year, int $month, int $day): int;

    /**
     * Converts a Julian Day Number into this calendar's year/month/day triple.
     *
     * @return array{0:int,1:int,2:int} [year, month, day]
     */
    public function fromJdn(int $jdn): array;

    /**
     * Determines whether the given year/month/day triple is a valid date
     * within this calendar (accounting for leap years and month lengths).
     */
    public function isValidDate(int $year, int $month, int $day): bool;

    /**
     * Returns whether the given year is a leap year in this calendar.
     */
    public function isLeapYear(int $year): bool;

    /**
     * Returns the number of days in the given month of the given year.
     */
    public function daysInMonth(int $year, int $month): int;

    /**
     * Returns the number of months per year in this calendar (always 12).
     */
    public function monthsInYear(): int;

    /**
     * Returns the localized/canonical names of the months, indexed from 1.
     *
     * @return array<int, string>
     */
    public function monthNames(): array;

    /**
     * Returns the localized/canonical names of the week days, indexed from 0 (start of week).
     *
     * @return array<int, string>
     */
    public function weekDayNames(): array;
}
