<?php

declare(strict_types=1);

namespace Aicrion\DateConverter\Calendars;

use Aicrion\DateConverter\Contracts\CalendarInterface;
use Aicrion\DateConverter\Enum\CalendarType;
use Aicrion\DateConverter\Exceptions\UnsupportedConversionException;

/**
 * Jalali (Persian / Shamsi) calendar implementation.
 *
 * Uses the astronomically accurate Birashk/Borkowski algorithm
 * (the same algorithmic base used by the widely trusted
 * jalaali-js / jalaali-php projects) which correctly accounts
 * for the irregular 33-year leap-year cycle of the Jalali calendar
 * across an extremely wide range of years.
 */
final class JalaliCalendar implements CalendarInterface
{
    /**
     * Break points of the 33-year leap cycle, precomputed for accuracy.
     *
     * @var array<int, int>
     */
    private const array BREAKS = [
        -61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210,
        1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178,
    ];

    private const array MONTH_NAMES = [
        1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر',
        5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان',
        9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند',
    ];

    private const array WEEK_DAY_NAMES = [
        0 => 'یکشنبه', 1 => 'دوشنبه', 2 => 'سه‌شنبه', 3 => 'چهارشنبه',
        4 => 'پنجشنبه', 5 => 'جمعه', 6 => 'شنبه',
    ];

    public function __construct(
        private readonly GregorianCalendar $gregorian = new GregorianCalendar(),
    ) {
    }

    public function type(): CalendarType
    {
        return CalendarType::Jalali;
    }

    /**
     * Computes leap status, corresponding Gregorian year, and the
     * Gregorian day of March on which Farvardin 1st falls, for a given Jalali year.
     *
     * @return array{0:bool,1:int,2:int} [isLeap, gregorianYear, marchDay]
     */
    private function jalCal(int $jalaliYear): array
    {
        $breaksCount = count(self::BREAKS);
        $gregorianYear = $jalaliYear + 621;

        if ($jalaliYear < self::BREAKS[0] || $jalaliYear >= self::BREAKS[$breaksCount - 1]) {
            throw new UnsupportedConversionException(
                CalendarType::Jalali,
                CalendarType::Gregorian,
                "Jalali year {$jalaliYear} is outside the supported algorithmic range"
            );
        }

        $leapJ = -14;
        $jp = self::BREAKS[0];
        $jump = 0;

        for ($i = 1; $i < $breaksCount; $i++) {
            $jm = self::BREAKS[$i];
            $jump = $jm - $jp;

            if ($jalaliYear < $jm) {
                break;
            }

            $leapJ += intdiv($jump, 33) * 8 + intdiv($jump % 33, 4);
            $jp = $jm;
        }

        $n = $jalaliYear - $jp;
        $leapJ += intdiv($n, 33) * 8 + intdiv(($n % 33) + 3, 4);

        if ($jump % 33 === 4 && $jump - $n === 4) {
            $leapJ += 1;
        }

        $leapG = intdiv($gregorianYear, 4) - intdiv((intdiv($gregorianYear, 100) + 1) * 3, 4) - 150;
        $march = 20 + $leapJ - $leapG;

        if ($jump - $n < 6) {
            $n = $n - $jump + intdiv($jump + 4, 33) * 33;
        }

        $leap = ((($n + 1) % 33) - 1) % 4 === 0;

        return [$leap, $gregorianYear, $march];
    }

    private function dayOfYear(int $month, int $day): int
    {
        if ($month <= 7) {
            return ($month - 1) * 31 + $day;
        }

        return 6 * 31 + ($month - 7) * 30 + $day;
    }

    public function toJdn(int $year, int $month, int $day): int
    {
        [, $gregorianYear, $march] = $this->jalCal($year);
        $doy = $this->dayOfYear($month, $day);

        return $this->gregorian->toJdn($gregorianYear, 3, $march) + $doy - 1;
    }

    public function fromJdn(int $jdn): array
    {
        [$gy] = $this->gregorian->fromJdn($jdn);
        $jy = $gy - 621;

        [, $gregorianYear, $march] = $this->jalCal($jy);
        $jdn1f = $this->gregorian->toJdn($gregorianYear, 3, $march);
        $k = $jdn - $jdn1f;

        if ($k < 0) {
            $jy -= 1;
            [, $gregorianYear, $march] = $this->jalCal($jy);
            $jdn1f = $this->gregorian->toJdn($gregorianYear, 3, $march);
            $k = $jdn - $jdn1f;
        }

        if ($k <= 185) {
            $jm = 1 + intdiv($k, 31);
            $jd = ($k % 31) + 1;

            return [$jy, $jm, $jd];
        }

        $k -= 186;
        $jm = 7 + intdiv($k, 30);
        $jd = ($k % 30) + 1;

        return [$jy, $jm, $jd];
    }

    public function isValidDate(int $year, int $month, int $day): bool
    {
        if ($year < CalendarType::Jalali->minYear() || $year > CalendarType::Jalali->maxYear()) {
            return false;
        }

        if ($month < 1 || $month > 12) {
            return false;
        }

        if ($day < 1) {
            return false;
        }

        try {
            return $day <= $this->daysInMonth($year, $month);
        } catch (UnsupportedConversionException) {
            return false;
        }
    }

    public function isLeapYear(int $year): bool
    {
        [$leap] = $this->jalCal($year);

        return $leap;
    }

    public function daysInMonth(int $year, int $month): int
    {
        if ($month <= 6) {
            return 31;
        }

        if ($month <= 11) {
            return 30;
        }

        return $this->isLeapYear($year) ? 30 : 29;
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
