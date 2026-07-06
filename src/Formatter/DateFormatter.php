<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Formatter;

use Aicrion\DateConverter\Contracts\CalendarInterface;
use Aicrion\DateConverter\ValueObjects\DateValue;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Formats a DateValue into strings, arrays, or native DateTimeImmutable
 * instances. Supports both PHP-style tokens (Y, m, d, n, j, M, l ...)
 * and a handful of Persian-friendly convenience patterns.
 *
 * Note: DateTimeImmutable conversion is only meaningful for Gregorian
 * dates since PHP's native DateTime does not model Jalali/Hijri years;
 * for non-Gregorian calendars the equivalent Gregorian instant is used.
 */
final class DateFormatter
{
    public function __construct(
        private readonly CalendarInterface $calendar,
    ) {
    }

    /**
     * Formats using PHP date()-style tokens: Y, y, m, n, d, j, M, F, D, l, N, w.
     */
    public function format(DateValue $date, string $pattern = 'Y-m-d'): string
    {
        $monthNames = $this->calendar->monthNames();
        $weekDayNames = $this->calendar->weekDayNames();
        $dayOfWeek = $this->dayOfWeek($date);

        $replacements = [
            'Y' => sprintf('%04d', $date->year),
            'y' => sprintf('%02d', $date->year % 100),
            'm' => sprintf('%02d', $date->month),
            'n' => (string) $date->month,
            'd' => sprintf('%02d', $date->day),
            'j' => (string) $date->day,
            'F' => $monthNames[$date->month] ?? (string) $date->month,
            'M' => mb_substr($monthNames[$date->month] ?? '', 0, 3),
            'l' => $weekDayNames[$dayOfWeek] ?? '',
            'D' => mb_substr($weekDayNames[$dayOfWeek] ?? '', 0, 3),
            'N' => (string) ($dayOfWeek === 0 ? 7 : $dayOfWeek),
            'w' => (string) $dayOfWeek,
        ];

        return strtr($pattern, $replacements);
    }

    /**
     * Computes the ISO-ish day of week (0 = Sunday ... 6 = Saturday)
     * for any calendar, via the shared JDN pivot.
     */
    public function dayOfWeek(DateValue $date): int
    {
        $jdn = $this->calendar->toJdn($date->year, $date->month, $date->day);

        return ($jdn + 1) % 7;
    }

    /**
     * @return array{year:int, month:int, day:int, calendar:string, formatted:string, month_name:string, week_day:string, is_leap_year:bool}
     */
    public function toArray(DateValue $date, string $pattern = 'Y-m-d'): array
    {
        $monthNames = $this->calendar->monthNames();
        $weekDayNames = $this->calendar->weekDayNames();

        return [
            'year' => $date->year,
            'month' => $date->month,
            'day' => $date->day,
            'calendar' => $date->calendar->value,
            'formatted' => $this->format($date, $pattern),
            'month_name' => $monthNames[$date->month] ?? '',
            'week_day' => $weekDayNames[$this->dayOfWeek($date)] ?? '',
            'is_leap_year' => $this->calendar->isLeapYear($date->year),
        ];
    }

    /**
     * Converts to the equivalent Gregorian DateTimeImmutable instant (via JDN).
     */
    public function toDateTime(DateValue $date): DateTimeImmutable
    {
        $jdn = $this->calendar->toJdn($date->year, $date->month, $date->day);
        $gregorian = new \Aicrion\DateConverter\Calendars\GregorianCalendar();
        [$gy, $gm, $gd] = $gregorian->fromJdn($jdn);

        return new DateTimeImmutable(sprintf('%04d-%02d-%02d', $gy, $gm, $gd), new DateTimeZone('UTC'));
    }
}
